<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Category>
 */
class CategoryFactory extends Factory
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
            'category_group_id' => $this->faker->uuid,
            'category_group_name' => $this->faker->word,
            'name' => $this->faker->word,
            'hidden' => $this->faker->boolean,
            'original_category_group_id' => $this->faker->uuid,
            'note' => $this->faker->sentence,
            'budgeted' => $this->faker->numberBetween(1000, 10000000),
            'activity' => $this->faker->numberBetween(1000, 10000000),
            'balance' => $this->faker->numberBetween(1000, 10000000),
            'goal_type' => $this->faker->randomElement(['TB', 'TBD', 'MF', 'NEED']),
            'goal_needs_whole_amount' => $this->faker->boolean,
            'goal_day' => $this->faker->numberBetween(1, 31),
            'goal_cadence' => $this->faker->numberBetween(1, 31),
            'goal_cadence_frequency' => $this->faker->numberBetween(1, 31),
            'goal_creation_month' => $this->faker->randomElement(['September', 'October', 'November', 'December']),
            'goal_target' => $this->faker->numberBetween(1000, 10000000),
            'goal_percentage_complete' => $this->faker->numberBetween(1, 100),
            'goal_months_to_budget' => $this->faker->numberBetween(1, 12),
            'goal_under_funded' => $this->faker->numberBetween(1000, 10000000),
            'goal_overall_funded' => $this->faker->numberBetween(1000, 10000000),
            'goal_overall_left' => $this->faker->numberBetween(1000, 10000000),
            'deleted' => false,
        ];
    }
}
