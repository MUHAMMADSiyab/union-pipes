<?php

namespace App\Http\Requests;

use App\Models\Product;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class InvoiceRequest extends FormRequest
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
            'invoice_no' => 'required',
            'buyer' => 'required',
            'address' => 'required|max:50|min:5',
            'ntn_no' => 'required',
            'gst_no' => 'required',
            'product' => ['required', Rule::in(Product::pluck('name')->toArray())],
            'quantity' => 'required|numeric',
            'rate' => 'required|numeric',
            'sales_tax_rate' => 'required|numeric',
            'date' => 'required|date',
        ];
    }
}
