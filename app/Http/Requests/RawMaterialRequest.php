<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RawMaterialRequest extends FormRequest
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
            'month' => ['required', 'date'],
            'entries' => ['required', 'array', 'min:0'],
            'entries.*.quality' => ['required', 'max:100'],
            'entries.*.bag' => ['required', 'numeric'],
            'entries.*.kg' => ['required', 'numeric'],
            'entries.*.weight' => ['required', 'numeric'],
            'entries.*.rate' => ['required', 'numeric'],
            'entries.*.amount' => ['required', 'numeric'],

        ];
    }
}
