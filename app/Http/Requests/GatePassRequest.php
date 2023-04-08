<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class GatePassRequest extends FormRequest
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
            'receiver' => 'required|max:150',
            'driver_name' => 'required|max:150',
            'vehicle_no' => 'required|max:50',
            'in_time' => 'nullable|date_format:H:i',
            'out_time' => 'nullable|date_format:H:i',
            'items' => 'required|array',
            'items.*.particular' => 'required',
            'items.*.quantity' => 'required|numeric',
            'items.*.remarks' => 'nullable|max:200',
        ];
    }

    public function attributes()
    {
        return [
            'items.*.particular' => 'particular',
            'items.*.quantity' => 'quantity',
            'items.*.remarks' => 'remarks',
        ];
    }
}
