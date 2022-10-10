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
            'sell_date' => 'date|required',
            'initial_readings' => 'required|array',
            'initial_readings.*.nozzle_id' => 'required|exists:nozzles,id',
            'initial_readings.*.value' => 'required|numeric',
        ];
    }

    public function attributes()
    {
        return [
            'sell_date' => 'Sell Date',
            'initial_readings' => 'Initial Readings',
            'initial_readings.*.nozzle_id' => 'Nozzle',
            'initial_readings.*.value' => 'Value',
        ];
    }
}
