<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PartnerRequest extends FormRequest
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
            'name' => 'required|max:30',
            'cnic' => 'nullable|min:9|max:30',
            'phone' => 'nullable|min:9|max:20',
            'address' => 'nullable|min:15|max:100',
            'photo' => 'nullable|image|max:2000',
        ];
    }
}
