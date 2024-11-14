<?php

namespace Database\Factories;

use DateTime;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Account>
 */
class AccountFactory extends Factory
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
            'name' => $this->faker->word,
            'type' => $this->faker->randomElement(['checking', 'savings', 'cash', 'creditCard', 'lineOfCredit', 'otherAsset', 'otherLiability', 'mortgage', 'autoLoan', 'studentLoan', 'personalLoan', 'medicalDebt', 'otherDebt']),
            'on_budget' => $this->faker->boolean,
            'closed' => $this->faker->boolean,
            'note' => $this->faker->sentence,
            'balance' => $this->faker->randomNumber(),
            'cleared_balance' => $this->faker->randomNumber(),
            'uncleared_balance' => $this->faker->randomNumber(),
            'transfer_payee_id' => $this->faker->uuid,
            'direct_import_linked' => $this->faker->boolean,
            'direct_import_in_error' => $this->faker->boolean,
            'last_reconciled_at' => $this->faker->dateTime()->format(DateTime::ATOM),
            'debt_original_balance' => $this->faker->randomNumber(),
            'debt_interest_rates' => [],
            'debt_minimum_payments' => [],
            'debt_escrow_amounts' => [],
            'deleted' => false,
        ];
    }
}
