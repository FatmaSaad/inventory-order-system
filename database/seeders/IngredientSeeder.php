<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Ingredient;
use App\Utils\Constants;
class IngredientSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        
        Ingredient::create([
            'name' => 'Beef',
            'stock' => 20000,
            'threshold' => 20000 * (Constants::INGREDIENT_LOW_LEVEL_PERCENTAGE / 100),
            'notification_sent' => false
        ]);
        Ingredient::create([
            'name' => 'Cheese',
            'stock' => 5000,
            'threshold' =>  5000 * (Constants::INGREDIENT_LOW_LEVEL_PERCENTAGE / 100),
            'notification_sent' => false
        ]);
        Ingredient::create([
            'name' => 'Onion',
            'stock' => 1000,
            'threshold' => 1000 * (Constants::INGREDIENT_LOW_LEVEL_PERCENTAGE / 100),
            'notification_sent' => false
        ]);
        // Ingredient::factory()->count(100)->create();
    }
}
