<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class MonthlySheetRequest extends FormRequest
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
            'previous_month_total' => ['required', 'numeric'],
            'entries' => ['required', 'array', 'min:0'],
            'entries.*.description' => ['required', 'max:100'],
            'entries.*.amount' => ['required', 'numeric'],

        ];
    }
}
