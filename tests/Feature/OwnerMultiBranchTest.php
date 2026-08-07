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
use Tests\TestCase;

class OwnerMultiBranchTest extends TestCase
{
    use RefreshDatabase;

    protected function seedBase(): void
    {
        $this->seed(RoleAndUserSeeder::class);
    }

    public function test_owner_can_switch_branch_and_scope_changes(): void
    {
        $this->seedBase();

        $owner = User::role('owner')->first();
        $this->assertNotNull($owner);

        $this->actingAs($owner);

        // Default: cabang pertama (Surabaya)
        $response = $this->get('/admin/dashboard/surabaya');
        $response->assertOk();
        $response->assertSee('Surabaya');

        // Pindah cabang ke Malang
        $this->post('/admin/switch-region/malang')
            ->assertRedirect(route('admin.dashboard', ['region' => 'malang']));

        $malang = Region::where('slug', 'malang')->first();
        $this->assertEquals($malang->id, session('selected_region_id'));

        $response = $this->get('/admin/dashboard/malang');
        $response->assertOk();
        $response->assertSee('Malang');
    }

    public function test_admin_cannot_switch_branch_and_is_isolated_to_own_region(): void
    {
        $this->seedBase();

        $admin = User::role('admin')->whereHas('region', fn ($q) => $q->where('slug', 'surabaya'))->first();

        // Admin tidak boleh memakai route switch-region (khusus owner)
        $this->actingAs($admin)
            ->post('/admin/switch-region/malang')
            ->assertForbidden();

        // Admin tetap terisolasi meskipun slug URL berisi cabang lain (anti bocor data)
        $response = $this->actingAs($admin)->get('/admin/dashboard/malang');
        $response->assertOk();
        $response->assertSee('Surabaya');
        $this->assertNull(session('selected_region_id'));
    }

    public function test_kurir_can_view_products_catalog(): void
    {
        $this->seedBase();

        $kurir = User::role('kurir')->whereHas('region', fn ($q) => $q->where('slug', 'surabaya'))->first();

        $response = $this->actingAs($kurir)->get('/kurir/products');
        $response->assertOk();
        $response->assertSee('Katalog Produk');
    }

    public function test_checkout_rejects_customer_from_another_branch(): void
    {
        $this->seedBase();
        CustomerCategory::firstOrCreate(['name' => 'Reseller']);

        $kurirSby = User::role('kurir')->whereHas('region', fn ($q) => $q->where('slug', 'surabaya'))->first();
        $malang = Region::where('slug', 'malang')->first();
        $category = CustomerCategory::firstOrCreate(['name' => 'Reseller']);
        $foreignCustomer = Customer::create([
            'name' => 'Customer Malang',
            'address' => 'Malang',
            'phone' => '628111',
            'region_id' => $malang->id,
            'customer_category_id' => $category->id,
            'added_by_user_id' => $kurirSby->id,
        ]);

        $response = $this->actingAs($kurirSby)->postJson('/kurir/orders/checkout', [
            'customer_id' => $foreignCustomer->id,
            'phone' => '628111',
            'address' => 'Malang',
            'payment_method' => 'cash',
            'note' => 'Test',
            'products' => json_encode([]),
        ]);

        $response->assertNotFound();
    }

    public function test_checkout_recalculates_price_from_database(): void
    {
        $this->seedBase();

        $kurir = User::role('kurir')->whereHas('region', fn ($q) => $q->where('slug', 'surabaya'))->first();
        $sby = Region::where('slug', 'surabaya')->first();
        $category = CustomerCategory::firstOrCreate(['name' => 'Reseller']);

        $customer = Customer::create([
            'name' => 'Toko Sari',
            'address' => 'Surabaya',
            'phone' => '6285555',
            'region_id' => $sby->id,
            'customer_category_id' => $category->id,
            'added_by_user_id' => $kurir->id,
        ]);

        $productCategory = Category::create(['name' => 'Produk', 'slug' => 'produk']);
        $product = Product::create([
            'name' => 'Kue Ijo Test',
            'category_id' => $productCategory->id,
            'region_id' => $sby->id,
            'description' => 'Test',
            'is_active' => true,
        ]);
        $variant = ProductVariant::create([
            'product_id' => $product->id,
            'name' => 'Isi 3 Kemasan',
            'price' => 9000,
            'is_active' => true,
        ]);

        // Kirim harga palsu 999 dari klien — server harus memakai harga DB (9000)
        $response = $this->actingAs($kurir)->post('/kurir/orders/checkout', [
            'customer_id' => $customer->id,
            'phone' => '6281111',
            'address' => 'Surabaya',
            'payment_method' => 'cash',
            'note' => null,
            'products' => json_encode([
                [
                    'product_id' => $product->id,
                    'product_name' => 'Kue Ijo Test',
                    'variant_id' => $variant->id,
                    'variant_name' => 'Isi 3 Kemasan',
                    'quantity' => 2,
                    'price' => 999,
                ],
            ]),
        ]);

        $response->assertOk();

        $order = Order::where('customer_id', $customer->id)->first();
        $this->assertNotNull($order);
        $this->assertEquals(18000, (int) $order->total_amount);

        $item = $order->items()->first();
        $this->assertEquals(9000, (int) $item->price);
        $this->assertEquals('Kue Ijo Test', $item->product_name);
        $this->assertEquals('Isi 3 Kemasan', $item->variant_name);
        $this->assertEquals($sby->id, $order->region_id);
    }
}
