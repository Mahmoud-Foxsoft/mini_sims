<?php

namespace Database\Factories;

use App\Models\PhoneMessage;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<PhoneMessage>
 */
class PhoneMessageFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'order_item_id' => $this->faker->numberBetween(1, 10),
            'message' => $this->faker->text(),
        ];
    }
}
