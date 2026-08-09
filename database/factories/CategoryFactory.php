<?php

namespace Database\Factories;

use App\Models\Category;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class CategoryFactory extends Factory
{
    protected $model = Category::class;

    public function definition(): array
    {
        $name = $this->faker->randomElement(['Produk', 'Hampers', 'Tumpeng']);

        return [
            'name' => $name,
            'slug' => Str::slug($name),
        ];
    }
}
