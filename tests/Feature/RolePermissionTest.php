<?php

namespace Tests\Feature;

use App\Models\Region;
use App\Models\User;
use Database\Seeders\RoleAndUserSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class RolePermissionTest extends TestCase
{
    use RefreshDatabase;

    protected function seedBase(): void
    {
        $this->seed(RoleAndUserSeeder::class);
    }

    public function test_permission_catalog_seeded_with_expected_names(): void
    {
        $this->seedBase();

        $expected = [
            'switch region',
            'manage products',
            'view products',
            'manage couriers',
            'manage customers',
            'manage orders',
            'view order history',
            'delete order history',
            'request return',
            'view performance',
            'export reports',
        ];

        $actual = Permission::pluck('name')->sort()->values()->all();
        sort($expected);

        $this->assertEquals($expected, $actual);
    }

    public function test_role_permission_matrix_is_correct(): void
    {
        $this->seedBase();

        $owner = Role::findByName('owner');
        $admin = Role::findByName('admin');
        $kurir = Role::findByName('kurir');

        $all = Permission::pluck('name')->all();
        $this->assertEqualsCanonicalizing($all, $owner->permissions->pluck('name')->all());

        $this->assertEqualsCanonicalizing([
            'manage products',
            'view products',
            'manage couriers',
            'manage customers',
            'manage orders',
            'view order history',
            'delete order history',
            'view performance',
            'export reports',
        ], $admin->permissions->pluck('name')->all());

        $this->assertEqualsCanonicalizing([
            'view products',
            'manage customers',
            'manage orders',
            'request return',
        ], $kurir->permissions->pluck('name')->all());

        // Owner harus punya semua permission, termasuk yang eksklusif
        $this->assertTrue($owner->hasPermissionTo('switch region'));
        $this->assertFalse($admin->hasPermissionTo('switch region'));
        $this->assertFalse($admin->hasPermissionTo('request return'));
        $this->assertFalse($kurir->hasPermissionTo('manage products'));
        $this->assertFalse($kurir->hasPermissionTo('switch region'));
    }

    public function test_admin_cannot_access_owner_only_switch_region_route(): void
    {
        $this->seedBase();

        $admin = User::role('admin')->whereHas('region', fn ($q) => $q->where('slug', 'surabaya'))->first();

        $this->actingAs($admin)
            ->post('/admin/switch-region/malang')
            ->assertForbidden();

        $this->assertFalse($admin->hasPermissionTo('switch region'));
    }

    public function test_admin_cannot_modify_or_delete_another_admin_via_courier_routes(): void
    {
        $this->seedBase();

        $sby = Region::where('slug', 'surabaya')->first();
        $adminA = User::factory()->create(['region_id' => $sby->id]);
        $adminA->assignRole('admin');
        $adminB = User::factory()->create(['region_id' => $sby->id]);
        $adminB->assignRole('admin');

        $this->actingAs($adminA)
            ->put('/admin/couriers/'.$adminB->id, ['name' => 'X', 'email' => 'x@example.com'])
            ->assertForbidden();

        $this->actingAs($adminA)
            ->delete('/admin/couriers/'.$adminB->id)
            ->assertForbidden();

        $this->actingAs($adminA)
            ->put('/admin/couriers/'.$adminB->id.'/note', ['note' => 'edit?'])
            ->assertForbidden();

        $this->actingAs($adminA)
            ->get('/admin/couriers/'.$adminB->id.'/performance-data')
            ->assertForbidden();

        // Admin A masih bisa hapus kurir sah di region yang sama
        $kurir = User::factory()->create(['region_id' => $sby->id]);
        $kurir->assignRole('kurir');

        $this->actingAs($adminA)
            ->delete('/admin/couriers/'.$kurir->id)
            ->assertRedirect(route('admin.couriers.index'));
    }

    public function test_admin_cannot_touch_kurir_from_other_branch_via_courier_routes(): void
    {
        $this->seedBase();

        $sby = Region::where('slug', 'surabaya')->first();
        $malang = Region::where('slug', 'malang')->first();
        $adminSby = User::role('admin')->whereHas('region', fn ($q) => $q->where('slug', 'surabaya'))->first();

        $kurirMalang = User::factory()->create(['region_id' => $malang->id]);
        $kurirMalang->assignRole('kurir');

        $this->actingAs($adminSby)
            ->put('/admin/couriers/'.$kurirMalang->id, ['name' => 'X', 'email' => 'x@example.com'])
            ->assertForbidden();

        $this->actingAs($adminSby)
            ->delete('/admin/couriers/'.$kurirMalang->id)
            ->assertForbidden();
    }

    public function test_kurir_has_own_scope_permissions_and_cannot_enter_admin_panel(): void
    {
        $this->seedBase();

        $kurir = User::role('kurir')->whereHas('region', fn ($q) => $q->where('slug', 'surabaya'))->first();

        $this->assertTrue($kurir->hasPermissionTo('view products'));
        $this->assertTrue($kurir->hasPermissionTo('manage customers'));
        $this->assertTrue($kurir->hasPermissionTo('manage orders'));
        $this->assertTrue($kurir->hasPermissionTo('request return'));
        $this->assertFalse($kurir->hasPermissionTo('manage products'));
        $this->assertFalse($kurir->hasPermissionTo('manage couriers'));

        // Kurir diblokir middleware role + permission pada panel admin
        $this->actingAs($kurir)->get('/admin/products')->assertForbidden();
        $this->actingAs($kurir)->get('/admin/couriers')->assertForbidden();
    }
}
