<?php

namespace App\Http\Controllers;

use App\Http\Requests\PaymentSettingRequest;
use App\Models\PaymentSetting;
use Illuminate\Http\Request;

class PaymentSettingController extends Controller
{
    /**
     * Get the payment settings row
     * @return Illuminate\Http\Response
     */
    public function get()
    {
        $payment_setting = PaymentSetting::first();

        if ($payment_setting) {
            return response()->json($payment_setting);
        }
    }

    public function currencies()
    {
        return response()->json(config('currencies'));
    }

    /**
     * Update the payment setting
     * @param App\Http\Requests\PaymentSettingRequest
     * @param App\Models\PaymentSetting $paymentSetting
     * @return Illuminate\Http\Response 
     */
    public function update(PaymentSettingRequest $request, PaymentSetting $paymentSetting)
    {
        if ($paymentSetting->update($request->validated())) {
            return response()->json(['success' => 'Payment setting updated successfully']);
        }
    }
}
