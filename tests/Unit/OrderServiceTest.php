<?php

namespace Tests\Unit;

use App\Models\Ingredient;
use App\Models\Order;
use App\Models\Product;
use App\Repositories\IngredientRepository;
use App\Services\OrderService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Mockery;
use Tests\TestCase;

class OrderServiceTest extends TestCase
{
    use RefreshDatabase;

    protected $orderService;
    protected $ingredientRepository;

    protected function setUp(): void
    {
        parent::setUp();

        $this->ingredientRepository = Mockery::mock(IngredientRepository::class);
        $this->orderService = new OrderService($this->ingredientRepository);
    }

    public function test_create_order_successfully_using_mock()
    {

        // Create a product with ingredients
        $product = Product::factory()->create();
        $ingredient1 = Ingredient::factory()->create(['stock' => 100]);
        $ingredient2 = Ingredient::factory()->create(['stock' => 50]);

        // Attach ingredients to the product
        $product->ingredients()->attach([
            $ingredient1->id => ['quantity' => 2], // 2 units of ingredient1 per product
            $ingredient2->id => ['quantity' => 1], // 1 unit of ingredient2 per product
        ]);

        // Prepare the order data
        $orderData = [
            'products' => [
                ['product_id' => $product->id, 'quantity' => 3], // Ordering 3 products
            ],
        ];

        // Create a mock for IngredientRepository
        $ingredientRepositoryMock = Mockery::mock(IngredientRepository::class);

        // Define the expectations for the `updateStock` method
        $ingredientRepositoryMock->shouldReceive('updateStock')
            ->once() // Expect a call for ingredient 1
            ->with(Mockery::on(fn($id) => $id > 0), 6) // ID must be a positive integer, and amount is 6 (3 * 2)
            ->andReturnUsing(function ($ingredientId, $amount) {
                return new Ingredient(['id' => $ingredientId, 'stock' => 100 - $amount]);
            });

        $ingredientRepositoryMock->shouldReceive('updateStock')
            ->once() // Expect a call for ingredient 2
            ->with(Mockery::on(fn($id) => $id > 0), 3) // ID must be a positive integer, and amount is 3 (3 * 1)
            ->andReturnUsing(function ($ingredientId, $amount) {
                return new Ingredient(['id' => $ingredientId, 'stock' => 50 - $amount]);
            });

        // Create an OrderService with the mock repository
        $orderService = new OrderService($ingredientRepositoryMock);

        // Call the `createOrder` method
        $order = $orderService->createOrder($orderData);

        // Assert that `updateStock` was called for each ingredient
        $ingredientRepositoryMock->shouldHaveReceived('updateStock')->with($ingredient1->id, 6)->once();
        $ingredientRepositoryMock->shouldHaveReceived('updateStock')->with($ingredient2->id, 3)->once();

        // Assert the order exists in the database
        $this->assertDatabaseHas('orders', ['id' => $order->id]);

        // Assert the order-product relationship exists
        $this->assertDatabaseHas('order_product', [
            'order_id' => $order->id,
            'product_id' => $product->id,
            'quantity' => 3,
        ]);
    }

   
}