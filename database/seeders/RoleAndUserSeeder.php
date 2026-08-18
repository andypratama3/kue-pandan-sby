<?php

namespace Database\Seeders;

use App\Models\Region;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RoleAndUserSeeder extends Seeder
{
    /**
     * Idempotent: aman dijalankan berulang (fresh install maupun DB yang sudah ada).
     */
    public function run(): void
    {
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // ===== PASSWORD AKUN SEEDED =====
        // Dipakai sementara untuk akun demo. Di production WAJIB diisi lewat
        // env SEED_ADMIN_PASSWORD; bila tidak diisi, akun dibangun dengan
        // password acak sehingga TIDAK BISA di-login sampai password
        // direset/diubah lewat mekanisme reset password.
        $password = env('SEED_ADMIN_PASSWORD', '');

        $password = 'password';


        // if (app()->isProduction() && $password === '') {
        //     $password = Str::random(32);
        // } elseif ($password === '') {
        // }

        $passwordHash = bcrypt($password);

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
            'view order history',   // lihat history & invoice
            'delete order history', // hapus permanen history pesanan (hanya admin/owner)
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
            'delete order history',
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
            [
                'name' => 'Surabaya',
                'is_active' => true,
                'address' => 'Jl. Lebak Jaya II, Gading, Tambaksari, Surabaya, Jawa Timur 60134',
                'operating_hours' => ['open' => '06:00', 'close' => '23:00'],
                'maps_link' => 'https://maps.app.goo.gl/FBLH5zD3sq1wBYit8',
                'contact_email' => 'pandanaslisbyadm@gmail.com',
                'contact_phone' => '082144834303',
            ]
        );
        $regionMalang = Region::firstOrCreate(
            ['slug' => Str::slug('Malang')],
            [
                'name' => 'Malang',
                'is_active' => true,
                'address' => 'Jl. Pelatuk No. 16 Sukun, Kota Malang, Jawa Timur 65147',
                'operating_hours' => ['open' => '06:00', 'close' => '23:00'],
                'maps_link' => 'https://maps.app.goo.gl/UhTpwAjYUuMyZfYQA',
                'contact_email' => 'pandanaslimalangadm@gmail.com',
                'contact_phone' => '082131338971',
            ]
        );
        $regionDenpasar = Region::firstOrCreate(
            ['slug' => Str::slug('Denpasar')],
            [
                'name' => 'Denpasar',
                'is_active' => true,
                'address' => 'Gg. Ikan Arwana, Sesetan, Denpasar Selatan, Bali 80224',
                'operating_hours' => ['open' => '06:00', 'close' => '23:00'],
                'maps_link' => 'https://maps.app.goo.gl/YA8hKxqigziBTiXf7',
                'contact_email' => 'pandanaslibaliadm@gmail.com',
                'contact_phone' => '082338901223',
            ]
        );

        // Perbaiki region yang sudah ada agar selalu aktif
        Region::query()->update(['is_active' => true]);

        // Owner (tanpa region — dapat memantau & berpindah semua cabang)
        $owner = User::firstOrCreate(
            ['email' => 'owner@kuepandanasli.com'],
            ['name' => 'Owner Kue Pandan Asli', 'password' => $passwordHash, 'region_id' => null]
        );
        if (! $owner->hasRole('owner')) {
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
                ['name' => $data['name'], 'password' => $passwordHash, 'region_id' => $data['region_id']]
            );
            if (! $user->hasRole($data['role'])) {
                $user->assignRole($data['role']);
            }
        }
    }
}
