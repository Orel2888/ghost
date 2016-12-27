<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use App\Ghost\Repositories\Goods\GoodsOrder;
use App\Jobs\MadePurchase;
use App\Events\WasPurchases;
use App\{
    Client,
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
    public function test_create_order_goods_price_not_found()
    {
        $models = $this->testTools->clientWithOrder();

        $this->goodsOrder->create([
            'goods_id'  => $models->goods->id,
            'client_id' => $models->client->id,
            'weight'    => $models->goods_price->weight,
            'comment'   => $models->client->comment,
            'cost'      => 25
        ]);
    }

    public function test_exists_order()
    {
        $models = $this->testTools->clientWithOrder();

        $this->assertTrue(
            $this->goodsOrder->existsOrder($models->order->client_id, $models->order->goods_id, $models->order->weight)
        );
    }

    public function test_exists_goods()
    {
        $models = $this->testTools->clientWithOrder();

        $this->assertTrue($this->goodsOrder->existsGoods($models->order->goods_id, $models->order->weight));
    }

    public function test_check_solvency()
    {
        $models = $this->testTools->clientWithOrder();

        $goodsPrice = $this->goodsOrder->getGoodsPrice($models->order);

        $models->order->client->update(['balance' => $models->goods_price->cost]);

        $this->assertInstanceOf(
            GoodsPrice::class,
            $this->goodsOrder->checkSolvency($models->order->id, $models->order->client->id)
        );
    }

    public function test_buy()
    {
        $models = $this->testTools->clientWithOrder();

        $goodsPrice = $this->goodsOrder->getGoodsPrice($models->order);

        $models->order->client->update(['balance' => $models->goods_price->cost]);

        $goodsPurchase = $this->goodsOrder->buy($models->order);

        $goodsPurchase->delete();
    }

    public function test_buy_processing_order()
    {
        $models = $this->testTools->clientWithOrder();

        $goodsPrice = $this->goodsOrder->getGoodsPrice($models->order);

        $models->order->client->update(['balance' => $models->goods_price->cost]);

        //$this->expectsJobs(MadePurchase::class);
        $this->expectsEvents(WasPurchases::class);

        $purchase = $this->goodsOrder->buyProcessingOrder($models->order);

        $this->assertInstanceOf(GoodsPurchase::class, $purchase);

        $purchase->delete();
    }

    public function test_terminate()
    {
        $this->testTools->cleaningTemporaryRows();

        echo 'Terminate';
    }
}
