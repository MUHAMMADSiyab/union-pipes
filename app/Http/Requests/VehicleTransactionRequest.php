<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class VehicleTransactionRequest extends FormRequest
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
            'vehicle_charges' => 'required|numeric',
            'expense' => 'numeric',
            'driver' => 'required|max:50',
            'date' => 'required',
            'vehicle_id' => 'required|exists:vehicles,id',
            'purchase_id' => 'nullable|exists:purchases,id',
        ];
    }

    public function attributes()
    {
        return [
            'vehicle_charges' => 'Vehicle Charges',
            'purchase_id' => 'Purchase',
            'vehicle' => 'Vehicle',
        ];
    }
}
