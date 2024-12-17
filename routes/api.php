<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\OrderController;

/**
 * Route to create a new order.
 * 
 * This POST route accepts order data and creates a new order through the
 * OrderController's store method.
 */
Route::post('/orders', [OrderController::class, 'store'])->name('orders.store');