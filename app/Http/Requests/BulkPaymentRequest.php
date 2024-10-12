<?php

namespace App\Http\Requests;

use App\Models\PaymentSetting;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class BulkPaymentRequest extends FormRequest
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

        $data = [
            'type' => ['required', Rule::in(['Sell', 'Purchase'])],
            'amount' => 'required|numeric|gt:0',
            'date' => 'required|date',
            'payment_method' => ['required', Rule::in($payment_setting->payment_methods)],
            'cheque_no' => 'nullable|max:30|min:5',
            'cheque_type' => ['nullable', Rule::in($payment_setting->cheque_types)],
            'cheque_due_date' => 'nullable|date',
            'cheque_images.*' => 'nullable|image|max:2000',
            'bank_id' => 'nullable|exists:banks,id',
            'description' => ['nullable', 'max:1000'],
        ];

        if ($this->type === 'Sell') {
            $data['customer_id'] = 'required|exists:customers,id';
            $data['company_id'] = 'nullable';
        } else if ($this->type === 'Purchase') {
            $data['company_id'] = 'required|exists:companies,id';
            $data['customer_id'] = 'nullable';
        }

        return $data;
    }
}
