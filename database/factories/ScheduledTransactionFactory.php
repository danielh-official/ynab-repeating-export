<?php

namespace Database\Factories;

use App\Enums\YnabAcceptedFrequency;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\ScheduledTransaction>
 */
class ScheduledTransactionFactory extends Factory
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
            'date_first' => $this->faker->date(),
            'date_next' => $this->faker->date(),
            'frequency' => $this->faker->randomElement(YnabAcceptedFrequency::cases())?->value,
            'amount' => $this->faker->numberBetween(1000, 10000000),
            'memo' => $this->faker->sentence(),
            'flag_color' => $this->faker->randomElement(['red', 'orange', 'yellow', 'green', 'blue', 'purple']),
            'account_id' => $this->faker->uuid,
            'payee_id' => $this->faker->uuid,
            'category_id' => $this->faker->uuid,
            'transfer_account_id' => $this->faker->uuid,
            'deleted' => false,
            'account_name' => $this->faker->word,
            'payee_name' => $this->faker->company,
            'category_name' => $this->faker->word,
        ];
    }
}
