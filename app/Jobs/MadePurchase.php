<?php

namespace App\Jobs;

use App\Jobs\Job;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Events\WasPurchases;

class MadePurchase extends Job implements ShouldQueue
{
    use InteractsWithQueue, SerializesModels;

    public $dataOrders;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($dataOrders)
    {
        //
        $this->dataOrders = $dataOrders;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        // Fire event about made purchase
        event(new WasPurchases($this->dataOrders));
    }
}
