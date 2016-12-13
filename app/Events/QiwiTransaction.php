<?php

namespace App\Events;

use App\Events\Event;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use App\QiwiTransaction as QiwiTransactionModel;

class QiwiTransaction extends Event
{
    use SerializesModels;

    public $transactions;

    /**
     * Create a new event instance.
     * @param $transactions QiwiTransactionModel
     * @return void
     */
    public function __construct($transactions)
    {
        //
        $this->transactions = $transactions;
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
