<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UserAccountEditRequest extends FormRequest
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

        $rules =  [
            'name' => 'required|max:40',
            'email' =>  Rule::unique('users', 'email')->ignore(auth()->id()),
        ];

        if (!empty(request('password'))) {
            $rules['password'] = 'min:6|max:100|confirmed';
        }

        return $rules;
    }
}
