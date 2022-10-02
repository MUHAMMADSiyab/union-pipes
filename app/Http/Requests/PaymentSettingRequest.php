<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class PaymentSettingRequest extends FormRequest
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
            'payment_methods' => 'required|array|min:1',
            'cheque_types' => 'required|array|min:1',
            'currency' => ['required', Rule::in(
                array_map(function ($item) {
                    return $item['code'];
                }, config('currencies'))
            )],
        ];
    }
}
