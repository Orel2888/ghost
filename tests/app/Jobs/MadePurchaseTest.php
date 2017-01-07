<?php

use App\Ghost\Repositories\Goods\GoodsOrder;
use App\Jobs\MadePurchase;
use App\Ghost\Repositories\Tg\Tg;

class MadePurchaseTest extends TestCase
{
    /**
     * @var GoodsOrder
     */
    public $goodsOrder;

    /**
     * @var Tg
     */
    public $tg;

    public function setUp()
    {
        parent::setUp();

        $this->goodsOrder = new GoodsOrder();
        $this->tg = new Tg();
    }

    public function test_made_purchase()
    {
        $models = $this->testTools->clientWithOrder();

        $models->client->update([
            'balance' => $models->goods_price->cost,
            'tg_chatid' => $this->tg->getClientsTesting()->first()->tg_chatid
        ]);

        $purchase = $this->goodsOrder->buy($models->order);

        dispatch(new MadePurchase(['orders_ids_successful' => [$models->order->id]]));
    }
}