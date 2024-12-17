<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Models\Product;
use App\Models\Ingredient;

class CreateOrderRequest extends FormRequest
{
    /**
     * Authorize the request.
     * 
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules for the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'products' => 'required|array',
            'products.*.product_id' => 'required|exists:products,id',
            'products.*.quantity' => 'required|integer|min:1',
        ];
    }

    /**
     * Add custom validation after the standard validation.
     *
     * @param \Illuminate\Contracts\Validation\Validator $validator
     * @return void
     */
    public function withValidator($validator)
    {
        // After performing the standard validation, check for stock availability
        $validator->after(function ($validator) {
            $this->checkStockAvailability($validator);
        });
    }

    /**
     * Check if there is sufficient stock for all ingredients in the order.
     * 
     * @param \Illuminate\Contracts\Validation\Validator $validator
     * @return void
     */
    private function checkStockAvailability($validator)
    {
        foreach ($this->input('products') as $productOrder) {
            $product = Product::find($productOrder['product_id']);

            if ($product) {
                foreach ($product->ingredients as $ingredient) {
                    $consumed = $ingredient->pivot->quantity * $productOrder['quantity']; // Calculate the consumed quantity based on the product quantity
                    if ($ingredient->stock < $consumed) {
                        $validator->errors()->add(
                            'products.' . $productOrder['product_id'] . '.quantity',
                            'Insufficient stock for ingredient: ' . $ingredient->name
                        );
                    }
                }
            }
        }
    }
}
