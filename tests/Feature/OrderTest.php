<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\Ingredient;
use App\Models\Order;
use App\Models\Product;
use App\Repositories\IngredientRepository;
use App\Services\OrderService;
use Mockery;

class OrderTest extends TestCase
{

    use RefreshDatabase;

    protected $orderService;
    protected $ingredientRepository;

    protected function setUp(): void
    {
        parent::setUp();

        $this->ingredientRepository = new IngredientRepository;
        $this->orderService = new OrderService($this->ingredientRepository);
    }

    public function test_create_order_successfully()
    {
        // Create products with ingredients and set their stock
        $ingredient1 = Ingredient::factory()->create(['stock' => 100]);
        $ingredient2 = Ingredient::factory()->create(['stock' => 50]);

        $product = Product::factory()->create();
        $product->ingredients()->attach([
            $ingredient1->id => ['quantity' => 2],  // 2 units of ingredient1 needed per product
            $ingredient2->id => ['quantity' => 1],  // 1 unit of ingredient2 needed per product
        ]);

        // Prepare the order data with product ID and quantity
        $orderData = [
            'products' => [
                ['product_id' => $product->id, 'quantity' => 3],  // Order for 3 products
            ],
        ];

        // Call the API route to create an order
        $response = $this->postJson(route('orders.store'), $orderData);


        // Assert the order is created in the database
        $response->assertStatus(201);  // 201 Created
        $this->assertDatabaseHas('orders', ['id' => $response->json('data.id')]);
        // Assert the product is correctly attached to the order with quantity 3
        $this->assertDatabaseHas('order_product', [
            'order_id' => $response->json('data.id'),
            'product_id' => $product->id,
            'quantity' => 3,
        ]);

        // Assert that the stock of ingredients is updated correctly
        $this->assertDatabaseHas('ingredients', ['id' => $ingredient1->id, 'stock' => 94]); // 100 - (2 * 3)
        $this->assertDatabaseHas('ingredients', ['id' => $ingredient2->id, 'stock' => 47]); // 50 - (1 * 3)
    }

    public function test_create_order_fails_due_to_insufficient_stock()
    {
        // Create an ingredient with 0 stock
        $ingredient = Ingredient::factory()->create(['stock' => 0]);

        // Create a product and attach the ingredient to it
        $product = Product::factory()->create();
        $product->ingredients()->attach([
            $ingredient->id => ['quantity' => 1],  // Product uses 1 of the ingredient
        ]);

        // Prepare the order data with the product ID and quantity
        $orderData = [
            'products' => [
                ['product_id' => $product->id, 'quantity' => 2],  // Ordering 2 products
            ],
        ];

        // Attempt to create the order
        $response = $this->postJson(route('orders.store'), $orderData);

        // Assert the response status is 422 (Unprocessable Entity) for validation failure
        $response->assertStatus(422);

        // Assert that an error message 
        $response->assertJsonFragment([
            'message' => 'Insufficient stock for ingredient: ' . $ingredient->name,
        ]);

        // Ensure no order was created in the database
        $this->assertDatabaseMissing('orders', [
            'id' => 1,  
        ]);
    }
    
}