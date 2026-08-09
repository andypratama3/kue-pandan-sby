<?php

namespace Tests\Feature;

use App\Models\Customer;
use App\Models\Order;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\Region;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class FileUploadValidationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Storage::fake('public');

        // Create roles
        \Spatie\Permission\Models\Role::create(['name' => 'owner']);
        \Spatie\Permission\Models\Role::create(['name' => 'admin']);
        \Spatie\Permission\Models\Role::create(['name' => 'kurir']);
    }

    /** @test */
    public function payment_proof_upload_rejects_fake_image_file()
    {
        $region = Region::factory()->create();
        $kurir = User::factory()->create(['region_id' => $region->id]);
        $kurir->assignRole('kurir');

        $customer = Customer::factory()->create([
            'region_id' => $region->id,
            'added_by_user_id' => $kurir->id,
        ]);

        $product = Product::factory()->create(['region_id' => $region->id]);
        $variant = ProductVariant::factory()->create(['product_id' => $product->id]);

        $order = Order::factory()->create([
            'customer_id' => $customer->id,
            'created_by_user_id' => $kurir->id,
            'region_id' => $region->id,
            'status' => 'diterima_pembeli',
        ]);

        // Create a fake image file (text file renamed to .jpg)
        $fakeImage = UploadedFile::fake()->create('fake.jpg', 100, 'text/plain');

        $response = $this->actingAs($kurir)
            ->postJson("/kurir/pesanan/{$order->id}/upload-proof", [
                'payment_proof' => $fakeImage,
            ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors('payment_proof');
    }

    /** @test */
    public function payment_proof_upload_accepts_valid_image()
    {
        $region = Region::factory()->create();
        $kurir = User::factory()->create(['region_id' => $region->id]);
        $kurir->assignRole('kurir');

        $customer = Customer::factory()->create([
            'region_id' => $region->id,
            'added_by_user_id' => $kurir->id,
        ]);

        $product = Product::factory()->create(['region_id' => $region->id]);
        $variant = ProductVariant::factory()->create(['product_id' => $product->id]);

        $order = Order::factory()->create([
            'customer_id' => $customer->id,
            'created_by_user_id' => $kurir->id,
            'region_id' => $region->id,
            'status' => 'diterima_pembeli',
        ]);

        // Create a valid image file
        $validImage = UploadedFile::fake()->image('payment.jpg');

        $response = $this->actingAs($kurir)
            ->postJson("/kurir/pesanan/{$order->id}/upload-proof", [
                'payment_proof' => $validImage,
            ]);

        $response->assertStatus(200);
        $this->assertEquals('selesai', $order->fresh()->status);
    }

    /** @test */
    public function return_proof_upload_rejects_fake_image_file()
    {
        $region = Region::factory()->create();
        $kurir = User::factory()->create(['region_id' => $region->id]);
        $kurir->assignRole('kurir');

        $customer = Customer::factory()->create([
            'region_id' => $region->id,
            'added_by_user_id' => $kurir->id,
        ]);

        $product = Product::factory()->create(['region_id' => $region->id]);
        $variant = ProductVariant::factory()->create(['product_id' => $product->id]);

        $order = Order::factory()->create([
            'customer_id' => $customer->id,
            'created_by_user_id' => $kurir->id,
            'region_id' => $region->id,
            'status' => 'diterima_pembeli',
        ]);

        // Request return first
        $this->actingAs($kurir)->postJson("/kurir/pesanan/{$order->id}/request-return", [
            'products' => [
                [
                    'product_id' => $product->id,
                    'product_variant_id' => $variant->id,
                    'quantity' => 1,
                ],
            ],
            'reason' => 'Produk rusak saat pengiriman',
        ]);

        // Create a fake image file (text file renamed to .jpg)
        $fakeImage = UploadedFile::fake()->create('fake.jpg', 100, 'text/plain');

        $response = $this->actingAs($kurir)
            ->postJson("/kurir/pesanan/{$order->id}/upload-return-proof", [
                'payment_proof' => $fakeImage,
            ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors('payment_proof');
    }

}
