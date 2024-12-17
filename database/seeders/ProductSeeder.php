<?php

namespace Database\Seeders;

use App\Models\Ingredient;
use App\Models\Product;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {

        // Create a Burger Product
        $burger = Product::create(['name' => 'Burger']);

        // Find Ingredients
        $beef = Ingredient::where('name', 'Beef')->first();
        $cheese = Ingredient::where('name', 'Cheese')->first();
        $onion = Ingredient::where('name', 'Onion')->first();

        // Attach Ingredients to the Burger with quantities
        $burger->ingredients()->attach([
            $beef->id => ['quantity' => 150],  // 150g Beef
            $cheese->id => ['quantity' => 30], // 30g Cheese
            $onion->id => ['quantity' => 20],  // 20g Onion
        ]);
    
        // Product::factory()->count(50)->create();

    }
}
