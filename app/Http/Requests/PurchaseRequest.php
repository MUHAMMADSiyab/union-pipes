<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PurchaseRequest extends FormRequest
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
            'date' => 'required',
            'invoice_no' => 'nullable|max:50',
            'company_id' => 'required|exists:companies,id',
            'sales_tax_percentage' => 'numeric',
            'category' => 'required|max:100',
            'description' => 'nullable|max:500',
            'items' => 'required_if:category,Raw Material|array',
            'items.*.purchase_item_id' => 'required_if:category,Raw Material',
            'items.*.quantity' => 'required_if:category,Raw Material|numeric',
            'items.*.rate' => 'required_if:category,Raw Material|numeric',
            'items.*.total' => 'required_if:category,Raw Material|numeric',
            'items.*.sales_tax' => 'required_if:category,Raw Material|numeric',
            'items.*.grand_total' => 'required_if:category,Raw Material|numeric',
        ];
    }

    public function attributes()
    {
        return [
            'items.*.purchase_item_id' => 'purchase item',
            'items.*.quantity' => 'quantity',
            'items.*.rate' => 'rate',
            'items.*.total' => 'total',
            'items.*.sales_tax' => 'sales tax',
            'items.*.grand_total' => 'grand total',
        ];
    }
}
