<?php

namespace Database\Factories;

use App\Utils\Constants;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Ingredient>
 */
class IngredientFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->word,
            'stock' => $this->faker->randomFloat(2, 5000, 20000), // Random stock between 5kg and 20kg
            'threshold' => function (array $attributes) {
                return $attributes['stock']  * (Constants::INGREDIENT_LOW_LEVEL_PERCENTAGE / 100);
            },
            'notification_sent' => false
        ];
    }
}
