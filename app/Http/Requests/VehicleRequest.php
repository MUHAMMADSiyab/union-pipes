<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class VehicleRequest extends FormRequest
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
    public function rules(): array
    {
        return [
            'registration_no' => 'required|max:10',
            'make' => 'nullable|max:20',
            'model' => 'nullable|max:20',
            'engine_no' => 'nullable|max:20',
            'chassis_no' => 'nullable|max:20',
            'contractor_name' => 'nullable|max:20',
            'calibration_date' => 'required|date',
            'receipt_no' => 'nullable|max:30',
            'validity' => 'required|date',
            'capacity' => 'numeric',
            'chambers.*.capacity' => 'required|numeric',
            'chambers.*.dip_value' => 'required|numeric'
        ];
    }

    public function attributes(): array
    {
        return [
            'chambers.*.capacity' => 'Capacity',
            'chambers.*.dip_value' => 'Dip Value'
        ];
    }
}
