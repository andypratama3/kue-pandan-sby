<?php

namespace Database\Seeders;

use App\Models\CustomerCategory;
use Illuminate\Database\Seeder;

class CustomerCategorySeeder extends Seeder
{
    public function run(): void
    {
        CustomerCategory::create(['name' => 'Reseller']);
        CustomerCategory::create(['name' => 'Supermarket']);
    }
}
