<?php

namespace App\Services;

use App\Http\Requests\CreateOrderRequest;
use App\Models\Order;
use App\Models\Product;
use App\Repositories\IngredientRepository;

class OrderService
{
    protected $ingredientRepository;

    /**
     * OrderService constructor.
     *
     * @param  \App\Repositories\IngredientRepository  $ingredientRepository
     * @return void
     */
    public function __construct(IngredientRepository $ingredientRepository)
    {
        $this->ingredientRepository = $ingredientRepository;
    }

    /**
     * Create a new order and update ingredient stock.
     *
     * @param  CreateOrderRequest  $orderData
     * @return \App\Models\Order
     */
    public function createOrder($orderData)
    {
        // Create a new order instance
        $order = Order::create();

        // Process each product in the order
        foreach ($orderData['products'] as $productOrder) {
            $product = Product::findOrFail($productOrder['product_id']);
            $quantity = $productOrder['quantity'];

            // Attach the product to the order with quantity
            $order->products()->attach($product->id, ['quantity' => $quantity]);

            // Update the stock for each ingredient based on the product quantity
            foreach ($product->ingredients as $ingredient) {
                $consumed = $ingredient->pivot->quantity * $quantity;
                $this->ingredientRepository->updateStock($ingredient->id, $consumed);
            }
        }

        return $order->load('products');
    }
}
