<?php

namespace App\Events;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class PaymentUpdated
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $payment_id;
    public $paymentable_id;
    public $investments;
    public $old_investments;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct($payment_id, $paymentable_id, $investments, $old_investments)
    {
        $this->payment_id = $payment_id;
        $this->paymentable_id = $paymentable_id;
        $this->investments = json_decode($investments, JSON_UNESCAPED_SLASHES);;
        $this->old_investments = json_decode($old_investments, JSON_UNESCAPED_SLASHES);;
    }
}
