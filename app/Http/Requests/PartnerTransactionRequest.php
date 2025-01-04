<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PartnerTransactionRequest extends FormRequest
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
            'title' => "required|min:3|max:200",
            "type" => "required|in:Debit,Credit",
            'partner_id' => "required|exists:partners,id",
            'amount' => 'required|numeric',
            'description' => 'nullable|max:500',
        ];
    }
}
