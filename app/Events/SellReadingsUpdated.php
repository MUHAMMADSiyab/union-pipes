<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class SellReadingsUpdated
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $readings;
    public $sell;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct($readings, $sell)
    {
        $this->readings = $readings;
        $this->sell = $sell;
    }
}
