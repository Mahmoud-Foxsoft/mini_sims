<?php

namespace Database\Factories;

use App\Models\Payment;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Payment>
 */
class PaymentFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => 1,
            'amount' => $this->faker->randomFloat(2, 0, 100),
            'currency' => $this->faker->currencyCode(),
            'transaction_id' => $this->faker->uuid(),
            'has_used' => $this->faker->boolean(),
            'status' => $this->faker->randomElement([Payment::WAITING_STATUS, Payment::CONFIRMING_STATUS, Payment::SENDING_STATUS, Payment::FINISHED_STATUS]),
            'paid_amount' => $this->faker->randomFloat(2, 0, 100),
        ];
    }
}
