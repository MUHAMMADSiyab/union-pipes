<?php

namespace App\Http\Requests;

use App\Models\Product;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class AccountRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'customer_id' => 'required|exists:customers,id',
            'product_price' => 'numeric',
            'vehicle_no' => 'nullable|max:30',
            'product' => ['nullable', Rule::in(Product::pluck('name')->toArray())],
            'product_quantity' => 'numeric',
            'total_amount' => 'numeric',
            'date' => 'required|date',
        ];
    }

    public function  attributes()
    {
        return [
            'customer_id' => 'Customer',
            'product_quantity' => 'Product Quantity',
            'product_price' => 'Product Price',
            'vehicle_no' => 'Vehicle No.',
            'total_amount' => 'Total Amount'
        ];
    }
}
