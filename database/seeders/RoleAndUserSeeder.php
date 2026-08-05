<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Region;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Str;

class RoleAndUserSeeder extends Seeder
{
    public function run(): void
    {
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Buat Roles
        $adminRole = Role::create(['name' => 'admin']);
        $kurirRole = Role::create(['name' => 'kurir']);

        // Buat Regions
        $regionSurabaya = Region::create(['name' => 'Surabaya', 'slug' => Str::slug('Surabaya')]);
        $regionMalang = Region::create(['name' => 'Malang', 'slug' => Str::slug('Malang')]);
        $regionDenpasar = Region::create(['name' => 'Denpasar', 'slug' => Str::slug('Denpasar')]);

        // Buat Users dengan region_id
        User::create(['name' => 'Admin Surabaya', 'email' => 'pandanaslisbyadm@gmail.com', 'password' => bcrypt('password'), 'region_id' => $regionSurabaya->id])->assignRole($adminRole);
        User::create(['name' => 'Admin Malang', 'email' => 'pandanaslimalangadm@gmail.com', 'password' => bcrypt('password'), 'region_id' => $regionMalang->id])->assignRole($adminRole);
        User::create(['name' => 'Admin Denpasar', 'email' => 'pandanaslibaliadm@hmail.com', 'password' => bcrypt('password'), 'region_id' => $regionDenpasar->id])->assignRole($adminRole);

        User::create(['name' => 'Kurir Surabaya', 'email' => 'kurir.surabaya@example.com', 'password' => bcrypt('password'), 'region_id' => $regionSurabaya->id])->assignRole($kurirRole);
        User::create(['name' => 'Kurir Malang', 'email' => 'kurir.malang@example.com', 'password' => bcrypt('password'), 'region_id' => $regionMalang->id])->assignRole($kurirRole);
        User::create(['name' => 'Kurir Denpasar', 'email' => 'kurir.denpasar@example.com', 'password' => bcrypt('password'), 'region_id' => $regionDenpasar->id])->assignRole($kurirRole);
    }
}
