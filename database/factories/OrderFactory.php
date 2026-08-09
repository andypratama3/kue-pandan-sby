<?php

namespace Database\Factories;

use App\Models\Customer;
use App\Models\Order;
use App\Models\Region;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class OrderFactory extends Factory
{
    protected $model = Order::class;

    public function definition(): array
    {
        return [
            'invoice_number' => 'INV/'.date('dmy').'/'.sprintf('%02d', 1).'/'.sprintf('%03d', rand(1, 999)).'/'.sprintf('%03d', rand(1, 999)).'/'.sprintf('%03d', rand(1, 999)),
            'customer_id' => Customer::factory(),
            'phone' => '62'.$this->faker->numerify('8##########'),
            'address' => $this->faker->address(),
            'payment_method' => $this->faker->randomElement(['tunai', 'transfer']),
            'payment_proof' => null,
            'note' => null,
            'rejection_note' => null,
            'total_amount' => $this->faker->numberBetween(50000, 500000),
            'status' => 'baru',
            'created_by_user_id' => User::factory(),
            'region_id' => Region::factory(),
            'paid_at' => null,
            'picked_up_at' => null,
            'delivered_at' => null,
            'received_by_buyer_at' => null,
        ];
    }
}
