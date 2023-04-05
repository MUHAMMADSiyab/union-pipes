<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ReturnedSoldItemRequest extends FormRequest
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
            'date' => 'required|date',
            'returned_items' => 'required|array|min:1',
            'returned_items.*.sell_id' => 'required|exists:sells,id',
            'returned_items.*.product_id' => 'required',
            'returned_items.*.quantity' => 'required|numeric',
            'returned_items.*.weight' => 'required|numeric',
            'returned_items.*.rate' => 'required|numeric',
            'returned_items.*.total' => 'required|numeric',
        ];
    }

    public function attributes()
    {
        return [
            'returned_items.*.product_id' => 'sell item',
            'returned_items.*.quantity' => 'quantity',
            'returned_items.*.weight' => 'weight',
            'returned_items.*.rate' => 'rate',
            'returned_items.*.total' => 'total',
        ];
    }
}
