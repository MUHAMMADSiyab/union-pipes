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
            'date' => 'required|date',
            'invoice_no' => 'nullable|max:50',
            'company_id' => 'required|exists:companies,id',
            'sales_tax_percentage' => 'numeric',
            'category' => 'required|max:100',
            'items' => 'required|array',
            'items.*.purchase_item_id' => 'required|exists:purchase_items,id',
            'items.*.quantity' => 'required|numeric',
            'items.*.rate' => 'required|numeric',
            'items.*.total' => 'required|numeric',
            'items.*.sales_tax' => 'required|numeric',
            'items.*.grand_total' => 'required|numeric',
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
