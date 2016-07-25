<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use App\Ghost\Repositories\Goods\GoodsOrder;
use App\Ghost\Repositories\Goods\GoodsManager;
use App\City;
use App\Client;
use App\Miner;
use App\GoodsPrice;
use App\Goods;
use App\Ghost\Libs\GibberishAES;
use Faker\Factory as Faker;

class GoodsOrderTest extends TestCase
{
    use BaseTestsHelper;

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

        $faker = Faker::create();

        $clientName = $faker->name;

        $client = Client::create([
            'name'      => $clientName,
            'comment'   => $clientName
        ]);

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

        $order->client->delete();
    }

    public function test_exists_order()
    {
        $order = $this->createOrder();

        $this->assertTrue($this->goodsOrder->existsOrder($order->client_id, $order->goods_id, $order->weight));

        $order->client->delete();

    }

    public function test_exists_goods()
    {
        $order = $this->createOrder();

        $this->assertTrue($this->goodsOrder->existsGoods($order->goods_id, $order->weight));

        $order->client->delete();
    }

    public function test_check_solvency()
    {
        $order = $this->createOrder();

        $goodsPrice = $this->goodsOrder->getGoodsPrice($order);

        $order->client->update(['balance' => $goodsPrice->cost]);

        $this->assertInstanceOf(GoodsPrice::class, $this->goodsOrder->checkSolvency($order->id, $order->client->id));

        $order->client->delete();
    }

    public function test_buy()
    {
        $order = $this->createOrder();

        $goodsPrice = $this->goodsOrder->getGoodsPrice($order);

        $order->client->update(['balance' => $goodsPrice->cost]);

        $goodsPurchase = $this->goodsOrder->buy($order);

        $goodsPurchase->delete();

        $order->client->delete();
    }
}
