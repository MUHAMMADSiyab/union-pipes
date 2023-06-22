<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class PaymentAdded
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $payment_id;
    public $paymentable_id;
    public $investments;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct($payment_id, $paymentable_id, $investments)
    {
        $this->payment_id = $payment_id;
        $this->paymentable_id = $paymentable_id;
        $this->investments = json_decode($investments, JSON_UNESCAPED_SLASHES);;
    }
}
