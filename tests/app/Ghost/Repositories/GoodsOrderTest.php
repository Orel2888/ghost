<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use App\Ghost\Repositories\Goods\GoodsOrder;
use App\Ghost\Repositories\Goods\GoodsManager;
use App\Jobs\MadePurchase;
use App\Events\WasPurchases;
use App\{
    City,
    Client,
    Miner,
    GoodsPrice,
    Goods,
    GoodsPurchase
};

class GoodsOrderTest extends TestCase
{
    /**
     * @var GoodsOrder
     */
    private $goodsOrder;

    public function setUp()
    {
        parent::setUp();

        $this->goodsOrder = new GoodsOrder();
    }

    public function createOrder()
    {
        $goods = Goods::first();

        $client = Client::first();

        $goodsFirstPrice = $goods->goodsPrice()->first();

        return $this->goodsOrder->create([
            'goods_id'  => $goods->id,
            'client_id' => $client->id,
            'weight'    => $goodsFirstPrice->weight,
            'comment'   => $client->comment,
            'cost'      => $goodsFirstPrice->cost
        ]);
    }

    public function test_create_order()
    {
        $order = $this->createOrder();

        $order->delete();
    }

    /**
     * @expectedException App\Ghost\Repositories\Goods\Exceptions\GoodsNotFound
     */
    public function test_create_order_goods_not_found()
    {
        $goods = Goods::first();

        $client = Client::first();

        $goodsFirstPrice = $goods->goodsPrice()->first();

        $this->goodsOrder->create([
            'goods_id'  => $goods->id,
            'client_id' => $client->id,
            'weight'    => $goodsFirstPrice->weight,
            'comment'   => $client->comment,
            'cost'      => 25
        ]);
    }

    public function test_exists_order()
    {
        $order = $this->createOrder();

        $this->assertTrue($this->goodsOrder->existsOrder($order->client_id, $order->goods_id, $order->weight));

        $order->delete();
    }

    public function test_exists_goods()
    {
        $order = $this->createOrder();

        $this->assertTrue($this->goodsOrder->existsGoods($order->goods_id, $order->weight));

        $order->delete();
    }

    public function test_check_solvency()
    {
        $order = $this->createOrder();

        $goodsPrice = $this->goodsOrder->getGoodsPrice($order);

        $order->client->update(['balance' => $goodsPrice->cost]);

        $this->assertInstanceOf(GoodsPrice::class, $this->goodsOrder->checkSolvency($order->id, $order->client->id));

        $order->delete();
    }

    public function test_buy()
    {
        $order = $this->createOrder();

        $goodsPrice = $this->goodsOrder->getGoodsPrice($order);

        $order->client->update(['balance' => $goodsPrice->cost]);

        $goodsPurchase = $this->goodsOrder->buy($order);

        $goodsPurchase->delete();

        $order->delete();
    }

    public function test_buy_processing_order()
    {
        $order = $this->createOrder();

        $goodsPrice = $this->goodsOrder->getGoodsPrice($order);

        $order->client->update(['balance' => $goodsPrice->cost]);

        //$this->expectsJobs(MadePurchase::class);
        $this->expectsEvents(WasPurchases::class);

        $purchase = $this->goodsOrder->buyProcessingOrder($order);

        $this->assertInstanceOf(GoodsPurchase::class, $purchase);

        $order->delete();
        $purchase->delete();
    }
}
