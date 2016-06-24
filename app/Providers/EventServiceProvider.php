<?php

namespace App\Providers;

use Illuminate\Contracts\Events\Dispatcher as DispatcherContract;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use App\GoodsPrice;
use App\Miner;
use App\Goods;
use App\Ghost\Libs\GibberishAES;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event listener mappings for the application.
     *
     * @var array
     */
    protected $listen = [
        'App\Events\SomeEvent' => [
            'App\Listeners\EventListener',
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
            Miner::find($goodsPrice->miner_id)->increment('count_goods', 1);
        });

        GoodsPrice::creating(function ($goodsPrice) {
            $goodsPrice->address = GibberishAES::enc($goodsPrice->address, env('K5'));
        });
        GoodsPrice::updated(function ($goodsPrice) {
            $goodsPrice->address = GibberishAES::enc($goodsPrice->address, env('K5'));
        });

        Goods::creating(function ($goods) {
            $goods->name = GibberishAES::enc($goods->name, env('K5'));
        });

        Goods::updating(function ($goods) {
            $goods->name = GibberishAES::enc($goods->name, env('K5'));
        });
    }
}
