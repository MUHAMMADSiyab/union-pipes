<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StockRequest extends FormRequest
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
            'stock_item_id' => 'required|exists:stock_items,id',
            'date' => 'required|date',
            'quantity' => 'required|numeric',
            'per_unit_weight' => 'required|numeric',
            'description' => 'nullable|max:500',
        ];
    }
}
