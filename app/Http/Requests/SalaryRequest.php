<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SalaryRequest extends FormRequest
{
    protected function prepareForValidation()
    {
        if ($this->has('month'))
            $this->merge([
                'month' => $this->month . "-01"
            ]);
    }

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
            'month' => 'required|date',
            'additional_amount' => 'nullable|numeric',
            'deducted_amount' => 'nullable|numeric',
        ];
    }
}
