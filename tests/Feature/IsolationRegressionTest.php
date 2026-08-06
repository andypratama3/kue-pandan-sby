<?php

namespace Tests\Feature;

use App\Models\Customer;
use App\Models\CustomerCategory;
use App\Models\Order;
use App\Models\Region;
use App\Models\User;
use Database\Seeders\RoleAndUserSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class IsolationRegressionTest extends TestCase
{
    use RefreshDatabase;

    protected function seedBase(): void
    {
        $this->seed(RoleAndUserSeeder::class);
    }

    protected function userByRoleRegion(string $role, string $slug): User
    {
        return User::role($role)->whereHas('region', fn ($q) => $q->where('slug', $slug))->firstOrFail();
    }

    private function makeCustomer(User $creator, Region $region): Customer
    {
        $category = CustomerCategory::firstOrCreate(['name' => 'Reseller']);

        return Customer::create([
            'name' => 'Toko '.$region->slug,
            'company_name' => 'PT '.$region->slug,
            'address' => $region->slug,
            'phone' => '628'.rand(10000000, 99999999),
            'region_id' => $region->id,
            'customer_category_id' => $category->id,
            'added_by_user_id' => $creator->id,
        ]);
    }

    private function makeOrder(User $kurir, Region $region, Customer $customer, string $status = 'selesai'): Order
    {
        return Order::create([
            'invoice_number' => 'INV/TEST/'.uniqid(),
            'customer_id' => $customer->id,
            'phone' => $customer->phone,
            'address' => $customer->address,
            'total_amount' => 25000,
            'payment_method' => 'cash',
            'created_by_user_id' => $kurir->id,
            'region_id' => $region->id,
            'status' => $status,
        ]);
    }

    public function test_owner_can_verify_reject_and_destroy_order_in_selected_branch(): void
    {
        $this->seedBase();

        $owner = User::role('owner')->first();
        $kurirSby = $this->userByRoleRegion('kurir', 'surabaya');
        $sby = Region::where('slug', 'surabaya')->first();
        $customer = $this->makeCustomer($kurirSby, $sby);

        $order = $this->makeOrder($kurirSby, $sby, $customer, 'selesai');
        $rejectOrder = $this->makeOrder($kurirSby, $sby, $customer, 'selesai');
        $deleteOrder = $this->makeOrder($kurirSby, $sby, $customer, 'selesai');

        $this->actingAs($owner);

        // Default cabang owner = Surabaya
        $this->post("/admin/orders/{$order->id}/verify")
            ->assertOk()
            ->assertJson(['message' => 'Pesanan berhasil diverifikasi.']);
        $this->assertEquals('diverifikasi_admin', $order->fresh()->status);

        $this->post("/admin/orders/{$rejectOrder->id}/reject", ['rejection_note' => 'Bukti tidak jelas.'])
            ->assertRedirect(route('admin.orders.index'));
        $this->assertEquals('diterima_pembeli', $rejectOrder->fresh()->status);

        $this->delete("/admin/orders/{$deleteOrder->id}")
            ->assertOk()
            ->assertJson(['message' => 'Pesanan berhasil dihapus secara permanen.']);
        $this->assertDatabaseMissing('orders', ['id' => $deleteOrder->id]);

        // History destroy ikut ter-scope ke cabang aktif
        $historyOrder = $this->makeOrder($kurirSby, $sby, $customer, 'diverifikasi_admin');
        $this->delete("/admin/historys/{$historyOrder->id}")
            ->assertRedirect(route('admin.historys.index'));
        $this->assertDatabaseMissing('orders', ['id' => $historyOrder->id]);
    }

    public function test_admin_cannot_access_orders_or_invoice_from_other_branch(): void
    {
        $this->seedBase();

        $kurirSby = $this->userByRoleRegion('kurir', 'surabaya');
        $sby = Region::where('slug', 'surabaya')->first();
        $customer = $this->makeCustomer($kurirSby, $sby);
        $order = $this->makeOrder($kurirSby, $sby, $customer, 'selesai');
        $verified = $this->makeOrder($kurirSby, $sby, $customer, 'diverifikasi_admin');

        $adminMalang = $this->userByRoleRegion('admin', 'malang');
        $this->actingAs($adminMalang);

        // Verifikasi order cabang lain -> 404 (scope region)
        $this->post("/admin/orders/{$order->id}/verify")->assertNotFound();

        // Invoice & download history cabang lain -> 403
        $this->get("/admin/historys/{$verified->id}/invoice")->assertForbidden();
        $this->get("/admin/historys/{$verified->id}/download")->assertForbidden();
        $this->delete("/admin/historys/{$verified->id}")->assertNotFound();
    }

    public function test_admin_from_same_branch_can_access_invoice(): void
    {
        $this->seedBase();

        $kurirSby = $this->userByRoleRegion('kurir', 'surabaya');
        $sby = Region::where('slug', 'surabaya')->first();
        $customer = $this->makeCustomer($kurirSby, $sby);
        $verified = $this->makeOrder($kurirSby, $sby, $customer, 'diverifikasi_admin');

        $adminSby = $this->userByRoleRegion('admin', 'surabaya');
        $this->actingAs($adminSby)
            ->get("/admin/historys/{$verified->id}/invoice")
            ->assertOk();
    }

    public function test_owner_customers_uses_admin_views_and_redirects(): void
    {
        $this->seedBase();

        $owner = User::role('owner')->first();
        $this->actingAs($owner);

        $response = $this->get('/admin/customers');
        $response->assertOk();
        $response->assertViewIs('dashboard.admin.customers.index');

        $sby = Region::where('slug', 'surabaya')->first();

        $this->post('/admin/customers', [
            'name' => 'Toko Baru Owner',
            'company_name' => 'PT Baru',
            'address' => 'Surabaya',
            'phone' => '6281234567890',
            'customer_category_id' => CustomerCategory::firstOrCreate(['name' => 'Reseller'])->id,
            'opening_hours' => '08:00-17:00',
            'payment_type' => 'harian',
        ])->assertRedirect('/admin/customers');

        $this->assertDatabaseHas('customers', [
            'name' => 'Toko Baru Owner',
            'region_id' => $sby->id,
        ]);
    }

    public function test_admin_cannot_flag_or_download_rekap_customer_from_other_branch(): void
    {
        $this->seedBase();

        $kurirSby = $this->userByRoleRegion('kurir', 'surabaya');
        $sby = Region::where('slug', 'surabaya')->first();
        $customer = $this->makeCustomer($kurirSby, $sby);

        $adminMalang = $this->userByRoleRegion('admin', 'malang');
        $this->actingAs($adminMalang);

        $this->post("/admin/customers/{$customer->id}/flag")->assertForbidden();
        $this->get("/admin/customers/{$customer->id}/rekap/download?daterange=01-01-2026+-+31-12-2026")
            ->assertForbidden();
    }

    public function test_kurir_cannot_read_last_order_of_foreign_customer(): void
    {
        $this->seedBase();

        $kurirSby = $this->userByRoleRegion('kurir', 'surabaya');
        $kurirMalang = $this->userByRoleRegion('kurir', 'malang');
        $malang = Region::where('slug', 'malang')->first();

        // Customer milik kurir Malang
        $foreignCustomer = $this->makeCustomer($kurirMalang, $malang);
        $this->makeOrder($kurirMalang, $malang, $foreignCustomer, 'diverifikasi_admin');

        $this->actingAs($kurirSby)
            ->getJson("/kurir/customer/{$foreignCustomer->id}/last-order")
            ->assertOk()
            ->assertJsonCount(0, 'items');
    }

    public function test_kurir_cannot_request_return_for_order_of_another_kurir(): void
    {
        $this->seedBase();

        $kurirA = $this->userByRoleRegion('kurir', 'surabaya');
        $kurirB = $this->userByRoleRegion('kurir', 'malang');
        $malang = Region::where('slug', 'malang')->first();
        $customer = $this->makeCustomer($kurirB, $malang);
        $order = $this->makeOrder($kurirB, $malang, $customer, 'diterima_pembeli');

        $this->actingAs($kurirA)
            ->post("/kurir/pesanan/{$order->id}/request-return", [
                'return_quantities' => ['1-0' => 1],
                'reason' => 'Produk rusak saat pengiriman.',
            ])
            ->assertForbidden();
    }
}
