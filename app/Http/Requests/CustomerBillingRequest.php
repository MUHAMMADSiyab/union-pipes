<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CustomerBillingRequest extends FormRequest
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

    // https://www.facebook.com/watch/live/?ref=search&v=414662977547204

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'customer_id' => 'required|exists:customers,id',
            'from_date' => 'required|date',
            'to_date' => 'required|date',
        ];
    }

    public function attributes()
    {
        return [
            'customer_id' => 'Customer',
            'from_date' => 'From Date',
            'to_date' => 'To Date',
        ];
    }
}
