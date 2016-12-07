<?php

namespace App\Events;

use App\Events\Event;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class WasPurchases extends Event
{
    use SerializesModels;

    public $orders;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct($orders)
    {
        //
        $this->orders = $orders;
    }

    /**
     * Get the channels the event should be broadcast on.
     *
     * @return array
     */
    public function broadcastOn()
    {
        return [];
    }
}
