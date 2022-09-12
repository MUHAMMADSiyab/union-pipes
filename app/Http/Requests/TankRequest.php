<?php

namespace App\Http\Requests;

use App\Models\Product;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class TankRequest extends FormRequest
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
            'name' => 'required|max:50|min:3',
            'product_id' => 'required|exists:products,id',
            'limit' => 'required|numeric',
            'code' => 'nullable',
            'lower_limit' => 'required|numeric',
            'description' => 'nullable|max:500',
        ];
    }
}
