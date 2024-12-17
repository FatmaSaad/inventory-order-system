<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\CreateOrderRequest;
use App\Services\OrderService;
use App\Traits\ApiResponse;
use App\Http\Controllers\Controller;
use App\Http\Resources\OrderResource;

class OrderController extends Controller
{
    use ApiResponse;

    protected $orderService;

    /**
     * @param OrderService $orderService
     */
    public function __construct(OrderService $orderService)
    {
        $this->orderService = $orderService;
    }

    /**
     * Store a newly created order in the database.
     *
     * This endpoint creates a new order in the system. The request must contain
     * the necessary order data, such as product IDs, quantities.
     *
     * @bodyParam products array required An array of products in the order. Each product must include a product_id and quantity.
     * Example: [
     *      { "product_id": 1, "quantity": 2 },
     *      { "product_id": 2, "quantity": 3 }
     * ]
     *
     * @response 201 {
     *  "status": "success",
     *  "message": "Order created successfully",
     *  "data": {
     *      "id": 1,
     *      "products": [
     *          {
     *              "id": 1,
     *              "name": "Burger",
     *              "quantity": 2
     *          },
     *          {
     *              "id": 2,
     *              "name": "Fries",
     *              "quantity": 3
     *          }
     *      ],
     *      "created_at": "2024-12-17 18:03:37",
     *      "updated_at": "2024-12-17 18:03:37"
     *  }
     * }
     * @response 422 {
     *   "message": "The selected products.0.product_id is invalid.",
     *   "errors": {
     *     "products.0.product_id": [
     *       "The selected products.0.product_id is invalid."
     *     ]
     *   }
     * }
     */

     
    public function store(CreateOrderRequest $request)
    {
        // Create the order using the service
        $order = $this->orderService->createOrder($request);

        // Return a success response with the order resource
        return $this->successResponse(new OrderResource($order), 'Order created successfully', 201);
    }
}
