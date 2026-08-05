<?php

namespace Tests\Feature;

use App\Models\User;
use Database\Seeders\RoleAndUserSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminSmokeTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_pages_render(): void
    {
        $this->seed(RoleAndUserSeeder::class);

        $admin = User::role('admin')->first();
        $this->assertNotNull($admin, 'No admin user found after seeding.');

        $routes = [
            '/admin/orders',
            '/admin/historys',
            '/admin/couriers',
            '/admin/customers',
            '/admin/products',
            '/admin/peforma-kurir',
            '/admin/peforma-customer',
            '/admin/profile',
        ];

        foreach ($routes as $route) {
            $response = $this->actingAs($admin)->get($route);
            $this->assertEquals(200, $response->getStatusCode(), "Page failed to render: {$route}");
        }
    }
}
