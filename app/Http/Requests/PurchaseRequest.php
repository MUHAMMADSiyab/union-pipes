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
            'company_id' => 'required|exists:companies,id',
            'invoice_no' => 'nullable|max:50',
            'vehicle_id' => 'required|exists:vehicles,id',
            'petrol_quantity' => 'required|numeric',
            'diesel_quantity' => 'required|numeric',
            'petrol_price' => 'required|numeric',
            'diesel_price' => 'required|numeric',
            'total_amount' => 'required|numeric',
            'chamber_readings' => 'required|array',
            'chamber_readings.*.rod_value' => 'required|numeric',
            'chamber_readings.*.available_quantity' => 'required|numeric',
            'distributions' => 'required|array',
            'distributions.*.new_stock_quantity' => 'required|numeric',
        ];
    }

    public function attributes()
    {
        return [
            'company_id' => 'Company',
            'vehicle_id' => 'Vehicle',
            'chamber_readings' => 'Chamber Readings',
            'chamber_readings.*.rod_value' => 'Rod Value',
            'chamber_readings.*.available_quantity' => 'Available Quantity',
            'chamber_readings.*.excess_quantity' => 'Excess Quantity',
            'distributions.*.new_stock_quantity' => 'New Stock Qty.',
        ];
    }
}
