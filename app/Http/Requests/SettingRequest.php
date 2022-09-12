<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SettingRequest extends FormRequest
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
            'app_name' => 'required|min:3|max:50',
            'email' => 'nullable|email',
            'phone' => 'min:9|max:20',
            'fax' => 'nullable|max:20',
            'address' => 'min:15|max:100',
            'app_logo' => 'nullable|image|max:2000|dimensions:min_width=80,min_height=80',
        ];
    }
}
