<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SellRequest extends FormRequest
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
            'date' => 'required',
            'invoice_no' => 'nullable|max:50',
            'customer_id' => 'required|exists:customers,id',
            'sales_tax_percentage' => 'numeric',
            'category' => 'required|max:100',
            'unit' => 'required_if:category,Pipe|max:100',
            'description' => 'nullable|max:500',
            'items' => 'required_if:category,Pipe|array',
            'items.*.product_id' => 'required_if:category,Pipe',
            'items.*.quantity' => 'required_if:category,Pipe|numeric',
            'items.*.weight' => 'required_if:category,Pipe|numeric',
            'items.*.rate' => 'required_if:category,Pipe|numeric',
            'items.*.total' => 'required_if:category,Pipe|numeric',
            'items.*.sales_tax' => 'required_if:category,Pipe|numeric',
            'items.*.grand_total' => 'required_if:category,Pipe|numeric',
        ];
    }

    public function attributes()
    {
        return [
            'items.*.product_id' => 'sell item',
            'items.*.quantity' => 'quantity',
            'items.*.weight' => 'weight',
            'items.*.rate' => 'rate',
            'items.*.total' => 'total',
            'items.*.sales_tax' => 'sales tax',
            'items.*.grand_total' => 'grand total',
        ];
    }
}
