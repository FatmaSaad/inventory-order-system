<?php

namespace Tests\Unit;

use App\Models\Ingredient;
use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CreateOrderRequestTest extends TestCase
{
    use RefreshDatabase;

    public function test_order_request_validation_passes_with_valid_data()
    {
        $product = Product::factory()->create();

        $response = $this->postJson(route('orders.store'), [
            'products' => [
                ['product_id' => $product->id, 'quantity' => 1],
            ],
        ]);

        $response->assertStatus(201);
    }

    public function test_order_request_validation_fails_with_invalid_data()
    {
        $response = $this->postJson(route('orders.store'), [
            'products' => [
                ['product_id' => 999, 'quantity' => 1], // Invalid product_id
            ],
        ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['products.0.product_id']);
    }
    public function test_order_request_fails_due_to_insufficient_stock()
    {
        $product = Product::factory()->create();

        $product->ingredients()->attach(Ingredient::factory()->create(['stock' => 0]), [
            'quantity' => 1, // Insufficient stock
        ]);

        $response = $this->postJson(route('orders.store'), [
            'products' => [
                ['product_id' => $product->id, 'quantity' => 1],
            ],
        ]);
        $response->assertStatus(422);
        $response->assertJsonValidationErrors([
            "products.{$product->id}.quantity"
        ]);
    }

    
}