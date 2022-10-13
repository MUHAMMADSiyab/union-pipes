<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class FinalReadingRequest extends FormRequest
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
            'final_readings.*.meters.*.value' => 'required|numeric',
        ];
    }

    public function attributes()
    {
        return [
            'final_readings.*.meters.*.value' => 'Value',
        ];
    }
}
