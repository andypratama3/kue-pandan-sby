<?php

namespace Database\Factories;

use App\Models\CustomerCategory;
use Illuminate\Database\Eloquent\Factories\Factory;

class CustomerCategoryFactory extends Factory
{
    protected $model = CustomerCategory::class;

    public function definition(): array
    {
        return [
            'name' => $this->faker->randomElement(['Reseller', 'Supermarket']),
        ];
    }
}
