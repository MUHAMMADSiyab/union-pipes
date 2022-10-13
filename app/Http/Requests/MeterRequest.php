<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class MeterRequest extends FormRequest
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
            'name' => 'required|max:50',
            'dispenser_id' => 'required|exists:dispensers,id',
            'code' => 'nullable|max:50',
            'description' => 'nullable|max:500',
        ];
    }

    public function attributes()
    {
        return [
            'dispenser_id' => 'Dispenser'
        ];
    }
}
