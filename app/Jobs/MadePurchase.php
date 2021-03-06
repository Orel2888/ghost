<?php

namespace App\Jobs;

use Event;
use App\GoodsOrder;
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
     * @param $dataOrders array
     * @return void
     */
    public function __construct(array $dataOrders)
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
        // Get a purchases and load relations
        if (isset($this->dataOrders['orders_ids_successful'])) {
            $orders = GoodsOrder::findMany($this->dataOrders['orders_ids_successful']);

            foreach ($orders as $order) {
                $order->load('goods.city', 'client', 'purchase');
            }

            // Fire event about made purchase
            Event::fire(new WasPurchases($orders));
        }
    }
}