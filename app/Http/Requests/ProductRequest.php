<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ProductRequest extends FormRequest
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
            'name' => 'required|max:30',
            'size' => 'nullable|numeric',
            'type' => 'required|max:50',
            'per_kg_price' => 'nullable|numeric',
            'per_unit_weight' => 'nullable|numeric',
        ];
    }
}
