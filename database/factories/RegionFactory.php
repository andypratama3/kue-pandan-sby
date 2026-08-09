<?php

namespace Database\Factories;

use App\Models\Region;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class RegionFactory extends Factory
{
    protected $model = Region::class;

    public function definition(): array
    {
        $name = $this->faker->randomElement(['Surabaya', 'Malang', 'Denpasar', 'Jakarta', 'Bandung']);

        return [
            'name' => $name,
            'slug' => Str::slug($name),
            'is_active' => true,
            'escalation_contact_name' => $this->faker->name(),
            'escalation_contact_phone' => $this->faker->numerify('08##########'),
            'address' => $this->faker->address(),
            'operating_hours' => ['open' => '06:00', 'close' => '23:00'],
            'maps_link' => 'https://maps.google.com/test',
            'contact_email' => $this->faker->email(),
            'contact_phone' => $this->faker->numerify('08##########'),
        ];
    }
}
