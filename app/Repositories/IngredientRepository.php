<?php

namespace App\Repositories;

use App\Models\Ingredient;

namespace App\Repositories;

use App\Models\Ingredient;
use Exception;

class IngredientRepository
{
    /**
     * Update the stock of a given ingredient.
     *
     * @param  int  $ingredientId
     * @param  int  $amount
     * @return \App\Models\Ingredient
     */
    public function updateStock($ingredientId, $amount)
    {
        // Find the ingredient by ID and reduce its stock
        $ingredient = Ingredient::findOrFail($ingredientId);
           
        // Check if there's enough stock available
        if ($ingredient->stock < $amount) {
            
            throw new Exception('Insufficient stock'); // Throw an exception if the stock is insufficient
        }
        // Reduce the stock by the given amount
        $ingredient->stock -= $amount;
        $ingredient->save();

        return $ingredient;
    }
}
