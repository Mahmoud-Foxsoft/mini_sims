<?php

namespace Database\Factories;

use App\Models\Order;
use Illuminate\Database\Eloquent\Factories\Factory;

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
        return [
            'id' => $this->faker->uuid(),
            'user_id' => 1,
            'total_cent_price' => $this->faker->numberBetween(1000, 99999),
            'status' => $this->faker->randomElement(['pending', 'completed', 'partially_completed', 'cancelled']),
        ];
    }
}
