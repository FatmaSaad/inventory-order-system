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
     * @param CreateOrderRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(CreateOrderRequest $request)
    {
        $order = $this->orderService->createOrder($request);

        return $this->successResponse(new OrderResource($order), 'Order created successfully', 201);
    }
}
