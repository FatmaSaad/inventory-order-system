<?php

namespace Tests\Unit;

use App\Models\Ingredient;
use App\Repositories\IngredientRepository;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class IngredientRepositoryTest extends TestCase
{
    use RefreshDatabase;

    public function test_update_stock_successfully()
    {
        $ingredient = Ingredient::factory()->create(['stock' => 100]);

        $repository = new IngredientRepository();
        $updatedIngredient = $repository->updateStock($ingredient->id, 10);

        $this->assertEquals(90, $updatedIngredient->stock);
        $this->assertDatabaseHas('ingredients', ['id' => $ingredient->id, 'stock' => 90]);
    }

    public function test_update_stock_fails_for_invalid_ingredient()
    {
        $repository = new IngredientRepository();

        $this->expectException(ModelNotFoundException::class);
        $repository->updateStock(999, 10);
    }

    public function test_update_stock_throws_error_on_negative_stock()
    {
        $ingredient = Ingredient::factory()->create(['stock' => 5]);
    
        $repository = new IngredientRepository();
    
        // Expect an exception when trying to reduce stock by more than the available stock
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Insufficient stock');  
    
        $repository->updateStock($ingredient->id, 10);
    }
    
}