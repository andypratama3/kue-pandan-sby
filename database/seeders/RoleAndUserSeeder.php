<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Region;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Str;

class RoleAndUserSeeder extends Seeder
{
    /**
     * Idempotent: aman dijalankan berulang (fresh install maupun DB yang sudah ada).
     */
    public function run(): void
    {
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Buat Roles
        $ownerRole = Role::firstOrCreate(['name' => 'owner']);
        $adminRole = Role::firstOrCreate(['name' => 'admin']);
        $kurirRole = Role::firstOrCreate(['name' => 'kurir']);

        // ===== KATALOG PERMISSION =====
        // Owner -> semua permission. Admin -> operasional per cabang.
        // Kurir -> hanya lingkup kerja sendiri (katalog, customer & order sendiri, retur).
        $permissions = [
            'switch region',        // pindah cabang (khusus owner)
            'manage products',      // CRUD produk
            'view products',        // lihat katalog produk
            'manage couriers',      // CRUD kurir per cabang
            'manage customers',     // CRUD customer
            'manage orders',        // kelola pesanan (verifikasi/tolak/upload bukti)
            'view order history',   // lihat history & in voice
            'request return',       // ajukan retur
            'view performance',     // laporan performa kurir/customer
            'export reports',       // export PDF (rekap, invoice)
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        // Assign permission per role (sync = idempotent & menghapus yang tak terdaftar)
        $ownerRole->syncPermissions($permissions);

        $adminRole->syncPermissions([
            'manage products',
            'view products',
            'manage couriers',
            'manage customers',
            'manage orders',
            'view order history',
            'view performance',
            'export reports',
        ]);

        $kurirRole->syncPermissions([
            'view products',
            'manage customers',
            'manage orders',
            'request return',
        ]);

        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Buat Regions (aktif semua)
        $regionSurabaya = Region::firstOrCreate(
            ['slug' => Str::slug('Surabaya')],
            ['name' => 'Surabaya', 'is_active' => true]
        );
        $regionMalang = Region::firstOrCreate(
            ['slug' => Str::slug('Malang')],
            ['name' => 'Malang', 'is_active' => true]
        );
        $regionDenpasar = Region::firstOrCreate(
            ['slug' => Str::slug('Denpasar')],
            ['name' => 'Denpasar', 'is_active' => true]
        );

        // Perbaiki region yang sudah ada agar selalu aktif
        Region::query()->update(['is_active' => true]);

        // Owner (tanpa region — dapat memantau & berpindah semua cabang)
        $owner = User::firstOrCreate(
            ['email' => 'owner@kuepandanasli.com'],
            ['name' => 'Owner Kue Pandan Asli', 'password' => bcrypt('password'), 'region_id' => null]
        );
        if (!$owner->hasRole('owner')) {
            $owner->assignRole('owner');
        }

        $users = [
            ['name' => 'Admin Surabaya', 'email' => 'pandanaslisbyadm@gmail.com', 'region_id' => $regionSurabaya->id, 'role' => 'admin'],
            ['name' => 'Admin Malang', 'email' => 'pandanaslimalangadm@gmail.com', 'region_id' => $regionMalang->id, 'role' => 'admin'],
            // Email admin Denpasar sudah diperbaiki dari typo sebelumnya (hmail.com -> gmail.com)
            ['name' => 'Admin Denpasar', 'email' => 'pandanaslibaliadm@gmail.com', 'region_id' => $regionDenpasar->id, 'role' => 'admin'],
            ['name' => 'Kurir Surabaya', 'email' => 'kurir.surabaya@example.com', 'region_id' => $regionSurabaya->id, 'role' => 'kurir'],
            ['name' => 'Kurir Malang', 'email' => 'kurir.malang@example.com', 'region_id' => $regionMalang->id, 'role' => 'kurir'],
            ['name' => 'Kurir Denpasar', 'email' => 'kurir.denpasar@example.com', 'region_id' => $regionDenpasar->id, 'role' => 'kurir'],
        ];

        foreach ($users as $data) {
            $user = User::firstOrCreate(
                ['email' => $data['email']],
                ['name' => $data['name'], 'password' => bcrypt('password'), 'region_id' => $data['region_id']]
            );
            if (!$user->hasRole($data['role'])) {
                $user->assignRole($data['role']);
            }
        }
    }
}
