<?php

namespace Tests\Unit;

use App\Http\Resources\OrderResource;
use App\Models\Ingredient;
use App\Models\Order;
use App\Models\Product;
use App\Repositories\IngredientRepository;
use App\Services\OrderService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class OrderResourceTest extends TestCase
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
    public function test_order_resource_structure()
    {
        // Create products with ingredients and set their stock
        $ingredient1 = Ingredient::factory()->create(['stock' => 100]);
        $ingredient2 = Ingredient::factory()->create(['stock' => 50]);

        $product = Product::factory()->create();
        $product->ingredients()->attach([
            $ingredient1->id => ['quantity' => 2],  // 2 units of ingredient1 needed per product
            $ingredient2->id => ['quantity' => 1],  // 1 unit of ingredient2 needed per product
        ]);

        // Prepare the order data (1 product, quantity of 3)
        $orderData = [
            'products' => [
                ['product_id' => $product->id, 'quantity' => 3],
                ['product_id' => $product->id, 'quantity' => 2],
            ],
        ];

        // Call the createOrder method
        $order = $this->orderService->createOrder($orderData);
        $resource = new OrderResource($order);
        $data = $resource->toArray(request());
        $this->assertArrayHasKey('id', $data);
        $this->assertArrayHasKey('products', $data);
        $this->assertArrayHasKey('created_at', $data);
        $this->assertArrayHasKey('updated_at', $data);
        $this->assertCount(2, $data['products']);
    }
}