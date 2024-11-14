<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Subtransaction>
 */
class SubtransactionFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'id' => $this->faker->uuid,
            'scheduled_transaction_id' => $this->faker->uuid,
            'amount' => $this->faker->randomFloat(2, 0, 1000),
            'memo' => $this->faker->sentence,
            'payee_id' => $this->faker->uuid,
            'category_id' => $this->faker->uuid,
            'transfer_account_id' => $this->faker->uuid,
            'deleted' => false,
        ];
    }
}
