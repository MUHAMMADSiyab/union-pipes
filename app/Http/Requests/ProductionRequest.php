<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ProductionRequest extends FormRequest
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
            'machine_id' => 'required|exists:machines,id',
            'employee_id' => 'required|exists:employees,id',
            'product_id' => 'required|exists:products,id',
            'date' => 'required|date',
            'shift' => 'required|max:50',
            'weight' => 'required|numeric',
            'quantity' => 'required|numeric',
            'total_weight' => 'required|numeric'
        ];
    }
}
