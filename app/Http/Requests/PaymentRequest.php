<?php

namespace App\Http\Requests;

use App\Models\PaymentSetting;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class PaymentRequest extends FormRequest
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

        $payment_setting = PaymentSetting::first();

        $rules =  [
            'amount' => 'required|numeric',
            'payment_method' => ['required', Rule::in($payment_setting->payment_methods)],
            'cheque_no' => 'nullable|max:30|min:5',
            'cheque_type' => ['nullable', Rule::in($payment_setting->cheque_types)],
            'cheque_due_date' => 'nullable|date',
            'cheque_images.*' => 'nullable|image|max:2000',
            'payment_date' => 'required|date_format:Y-m-d H:i:s|date',
            'bank_id' => 'nullable|exists:banks,id',
            'description' => 'nullable|max:500'
        ];

        if (request()->method() !== 'PUT') {
            $rules['transaction_type'] = ['required', Rule::in(["Credit", "Debit"])];
        }

        return $rules;
    }

    /**
     * Set validation attributes
     *
     * @return array
     */
    public function attributes()
    {
        return [
            'bank_id' => "bank",
        ];
    }
}
