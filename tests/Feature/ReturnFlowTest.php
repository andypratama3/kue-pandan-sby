<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Customer;
use App\Models\CustomerCategory;
use App\Models\Order;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\Region;
use App\Models\User;
use Database\Seeders\RoleAndUserSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ReturnFlowTest extends TestCase
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

    private function makeOrderWithItems(User $kurir, Region $region, Customer $customer): Order
    {
        $productCategory = Category::firstOrCreate(['name' => 'Kue Basah'], ['slug' => 'kue-basah']);

        $productA = Product::create([
            'name' => 'Kue A',
            'description' => 'Kue A',
            'price' => 4000,
            'category_id' => $productCategory->id,
            'region_id' => $region->id,
        ]);
        $productB = Product::create([
            'name' => 'Kue B',
            'description' => 'Kue B',
            'price' => 10000,
            'category_id' => $productCategory->id,
            'region_id' => $region->id,
        ]);
        $variantB = ProductVariant::create([
            'product_id' => $productB->id,
            'name' => 'Toping Coklat',
            'price' => 10000,
        ]);

        $order = Order::create([
            'invoice_number' => 'INV/TEST/'.uniqid(),
            'customer_id' => $customer->id,
            'phone' => $customer->phone,
            'address' => $customer->address,
            'total_amount' => 18000,
            'payment_method' => 'tf',
            'created_by_user_id' => $kurir->id,
            'region_id' => $region->id,
            'status' => 'diterima_pembeli',
        ]);

        $order->items()->createMany([
            ['product_id' => $productA->id, 'product_name' => 'Kue A', 'variant_id' => null, 'variant_name' => null, 'quantity' => 2, 'price' => 4000, 'subtotal' => 8000],
            ['product_id' => $productB->id, 'product_name' => 'Kue B', 'variant_id' => $variantB->id, 'variant_name' => 'Toping Coklat', 'quantity' => 1, 'price' => 10000, 'subtotal' => 10000],
        ]);

        return $order;
    }

    public function test_full_return_lifecycle_with_edit_sync(): void
    {
        $this->seedBase();
        Storage::fake('local');

        $kurir = $this->userByRoleRegion('kurir', 'surabaya');
        $sby = Region::where('slug', 'surabaya')->first();
        $customer = $this->makeCustomer($kurir, $sby);
        $order = $this->makeOrderWithItems($kurir, $sby, $customer);

        $items = $order->items->keyBy(fn ($i) => $i->product_id.'-'.($i->variant_id ?? 0));
        $keyA = $items->filter(fn ($i) => $i->product_name === 'Kue A')->keys()->first();
        $keyB = $items->filter(fn ($i) => $i->product_name === 'Kue B')->keys()->first();
        $productAId = $items[$keyA]->product_id;

        $this->actingAs($kurir);

        // 1. Ajukan retur untuk 2 produk
        $this->post("/kurir/pesanan/{$order->id}/request-return", [
            'return_quantities' => [$keyA => 1, $keyB => 1],
            'reason' => 'Produk rusak saat pengiriman.',
        ])
            ->assertOk()
            ->assertJsonPath('order.status', 'menunggu_retur');

        $orderReturn = $order->returns()->latest()->first();
        $this->assertEquals('menunggu_konfirmasi', $orderReturn->status);
        $this->assertEquals(14000, (float) $orderReturn->total_amount_returned);

        // 2. Edit retur: kurangi qty Kue A dan HAPUS Kue B dari retur
        $this->post("/kurir/pesanan/{$order->id}/request-return/edit", [
            'order_return_id' => $orderReturn->id,
            'return_quantities' => [$keyA => 1],
            'reason' => 'Hanya Kue A yang rusak.',
        ])
            ->assertOk();

        // Produk Kue B harus ikut terhapus dari retur (sync), total pun ikut terupdate
        $this->assertDatabaseCount('order_return_products', 1);
        $this->assertDatabaseMissing('order_return_products', ['order_return_id' => $orderReturn->id, 'product_id' => $items[$keyB]->product_id]);
        $this->assertEquals(4000, (float) $orderReturn->fresh()->total_amount_returned);

        // 3. Unggah bukti retur
        $this->post("/kurir/pesanan/{$order->id}/upload-return-proof", [
            'payment_proof' => UploadedFile::fake()->image('return.jpg'),
        ])
            ->assertOk();

        $this->assertEquals('menunggu_verifikasi_admin', $order->fresh()->status);
        $this->assertNotNull($order->fresh()->returns()->first()->return_proof);

        // 4. Edit harus DITOLAK setelah bukti diunggah (sudah "dikirim" ke admin)
        $this->post("/kurir/pesanan/{$order->id}/request-return/edit", [
            'order_return_id' => $orderReturn->id,
            'return_quantities' => [$keyB => 1],
        ])->assertStatus(409);

        // 5. Admin verifikasi pesanan + retur
        $admin = $this->userByRoleRegion('admin', 'surabaya');
        $this->actingAs($admin)
            ->post("/admin/orders/{$order->id}/verify")
            ->assertOk()
            ->assertJson(['message' => 'Pesanan berhasil diverifikasi.']);

        $this->assertEquals('diverifikasi_admin', $order->fresh()->status);
        $this->assertEquals('diverifikasi', $orderReturn->fresh()->status);
        // Tanpa bukti pembayaran, pesanan tidak ditandai lunas (paid_at)
        $this->assertNull($order->fresh()->paid_at);
    }

    public function test_verify_cancels_pending_return_without_proof(): void
    {
        $this->seedBase();

        $kurir = $this->userByRoleRegion('kurir', 'surabaya');
        $sby = Region::where('slug', 'surabaya')->first();
        $customer = $this->makeCustomer($kurir, $sby);
        $order = $this->makeOrderWithItems($kurir, $sby, $customer);

        // Skema legacy/anomali: retur menggantung tanpa bukti, sementara order
        // sudah berstatus 'selesai' (bukti pembayaran diunggah).
        $order->update(['status' => 'selesai']);
        $order->update(['payment_proof' => 'payment_proofs/legacy.jpg']);
        $orderReturn = $order->returns()->create([
            'courier_id' => $kurir->id,
            'region_id' => $sby->id,
            'status' => 'menunggu_konfirmasi',
        ]);

        $admin = $this->userByRoleRegion('admin', 'surabaya');
        $this->actingAs($admin)
            ->post("/admin/orders/{$order->id}/verify")
            ->assertOk();

        // Retur tanpa bukti tidak boleh di-approve; dibatalkan agar tidak menggantung
        $this->assertEquals('ditolak', $orderReturn->fresh()->status);
        $this->assertEquals('diverifikasi_admin', $order->fresh()->status);
    }

    public function test_kurir_invalid_return_quantity_gets_422_not_500(): void
    {
        $this->seedBase();

        $kurir = $this->userByRoleRegion('kurir', 'surabaya');
        $sby = Region::where('slug', 'surabaya')->first();
        $customer = $this->makeCustomer($kurir, $sby);
        $order = $this->makeOrderWithItems($kurir, $sby, $customer);

        $this->actingAs($kurir)
            ->post("/kurir/pesanan/{$order->id}/request-return", [
                'return_quantities' => ['99999-0' => 1],
                'reason' => 'Produk rusak saat pengiriman.',
            ])
            ->assertStatus(422)
            ->assertJsonStructure(['errors' => ['return_quantities']]);

        $this->assertDatabaseCount('order_returns', 0);
    }
}
