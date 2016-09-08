<?php

namespace App\Providers;

use Illuminate\Contracts\Events\Dispatcher as DispatcherContract;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use App\GoodsPrice;
use App\GoodsPurchase;
use App\Miner;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event listener mappings for the application.
     *
     * @var array
     */
    protected $listen = [
        'App\Events\Install' => [
            'App\Listeners\InstallStep',
        ],
    ];

    /**
     * Register any other events for your application.
     *
     * @param  \Illuminate\Contracts\Events\Dispatcher  $events
     * @return void
     */
    public function boot(DispatcherContract $events)
    {
        parent::boot($events);

        GoodsPrice::created(function ($goodsPrice) {
            $miner = Miner::find($goodsPrice->miner_id);

            $miner->increment('counter_goods');
            $miner->increment('counter_total_goods');
        });

        GoodsPurchase::created(function ($goodsPurchase) {
            $miner = Miner::find($goodsPurchase->miner_id);

            $miner->increment('counter_goods_ok');
            $miner->decrement('counter_goods');

            $miner->increment('balance', $miner->ante);
        });

        GoodsPurchase::updated(function ($goodsPurchase) {
            if ($goodsPurchase->status == 2) {
                $miner = Miner::find($goodsPurchase->miner_id);

                $miner->decrement('counter_goods_ok');
                $miner->increment('counter_goods_fail');
            }
        });
    }
}
