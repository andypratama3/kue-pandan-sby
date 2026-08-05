<?php

namespace Tests\Feature;

use App\Models\User;
use Tests\TestCase;

class AdminSmokeTest extends TestCase
{
    public function test_admin_pages_render(): void
    {
        $admin = User::role('admin')->first();
        $this->assertNotNull($admin, 'No admin user found in database.');

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
