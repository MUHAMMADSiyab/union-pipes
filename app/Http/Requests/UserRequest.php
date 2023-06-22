<?php

namespace App\Http\Requests;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UserRequest extends FormRequest
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
        $rules = [
            'name' => 'required|max:40',
            'roles' => 'required|array|min:1',
        ];

        if (!empty($userId = request()->segment(3)) && request()->method() === "PUT") { // If edit
            $rules['email'] = [
                'required',
                'max:50',
                'email',
                Rule::unique('users', 'email')->ignore($userId),
            ];

            if (!empty(request('password'))) {
                $rules['password'] = 'min:6|max:100|confirmed';
            }
        } else {
            $rules['email'] = 'required|max:50|email|unique:users,email';
            $rules['password'] = 'required|min:6|max:100|confirmed';
        }


        return $rules;
    }
}
