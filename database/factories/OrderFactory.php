<?php

namespace Database\Factories;

use App\Models\Order;
use App\Models\Service;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Order>
 */
class OrderFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $price = fake()->randomElement([150000, 300000, 750000]);
        $fee = $price * 0.05;

        return [
            'order_number' => 'INV-' . strtoupper(Str::random(8)),
            'customer_id' => User::factory(),
            'provider_id' => User::factory(),
            'service_id' => Service::factory(),
            'price' => $price,
            'platform_fee' => $fee,
            'total_amount' => $price + $fee,
            'status' => 'completed',
            'started_at' => now()->subDays(5),
            'completed_at' => now()->subDay(),
        ];
    }
}
