<?php

namespace Database\Factories;

use App\Models\Customer;
use App\Models\CustomerCategory;
use App\Models\Region;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class CustomerFactory extends Factory
{
    protected $model = Customer::class;

    public function definition(): array
    {
        return [
            'name' => $this->faker->company(),
            'company_name' => $this->faker->company(),
            'address' => $this->faker->address(),
            'landmark' => $this->faker->streetName(),
            'phone' => '62'.$this->faker->numerify('8##########'),
            'opening_hours' => $this->faker->randomElement(['08:00-17:00', '09:00-18:00', '10:00-22:00']),
            'payment_type' => $this->faker->randomElement(['tunai', 'transfer']),
            'note' => null,
            'region_id' => Region::factory(),
            'customer_category_id' => CustomerCategory::first()?->id ?? CustomerCategory::factory(),
            'added_by_user_id' => User::factory(),
            'is_flagged' => false,
        ];
    }
}
