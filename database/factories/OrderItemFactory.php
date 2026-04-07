<?php

namespace Database\Factories;

use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<OrderItem>
 */
class OrderItemFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'order_id' => Order::first()->id,
            'user_id' => 1,
            'external_order_id' => $this->faker->uuid(),
            'service_name' => $this->faker->company(),
            'phone_number' => $this->faker->phoneNumber(),
            'price_cents' => $this->faker->randomNumber(4),
        ];
    }
}
