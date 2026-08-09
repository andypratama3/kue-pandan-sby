<?php

namespace Database\Factories;

use App\Models\Category;
use App\Models\Product;
use App\Models\Region;
use Illuminate\Database\Eloquent\Factories\Factory;

class ProductFactory extends Factory
{
    protected $model = Product::class;

    public function definition(): array
    {
        return [
            'name' => $this->faker->words(3, true),
            'description' => $this->faker->sentence(),
            'image_path' => null,
            'tag' => $this->faker->word(),
            'is_active' => true,
            'category_id' => Category::factory(),
            'region_id' => Region::factory(),
        ];
    }
}
