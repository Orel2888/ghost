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
use App\Ghost\Libs\GibberishAES;

class GoodsOrderTest extends TestCase
{
    use BaseTestsHelper;

    /**
     * @var GoodsOrder
     */
    private $goodsOrder;

    protected static $models = [];

    public function setUp()
    {
        parent::setUp();

        $this->goodsOrder = new GoodsOrder();

        if (!count(self::$models)) {
            $city = City::create(['name' => 'Новосибирск']);
            $client = Client::create(['name' => 'Vasya', 'comment' => 123456]);

            $goodsManager = new GoodsManager();

            $goods = $goodsManager->addGoods([
                'goods_name'    => 'Бананы',
                'city_name'     => $city->name
            ]);

            $miner = Miner::create([
                'name'  => 'Vadim'
            ]);

            $goodsPrice = $goodsManager->addGoodsPrice([
                'miner_id'  => $miner->id,
                'goods_id'  => $goods->id,
                'weight'    => '0.5',
                'address'   => 'The backup and restore operations for global variables and static',
                'cost'      => 1000
            ]);

            self::$models = [
                'city'      => $city,
                'client'    => $client,
                'goods'     => $goods,
                'goods_price' => $goodsPrice,
                'miner'     => $miner
            ];
        }
    }

    public function cleanTable()
    {
        self::$models['city']->delete();
        self::$models['client']->delete();
        self::$models['miner']->delete();
    }

    public function test_create_order()
    {
        $order = $this->goodsOrder->create([
            'goods_id'  => self::$models['goods']->id,
            'client_id' => self::$models['client']->id,
            'weight'    => self::$models['goods_price']->weight,
            'comment'   => self::$models['client']->comment
        ]);

        $order->delete();
    }

    public function test_exists_order()
    {
        $order = $this->goodsOrder->create([
            'goods_id'  => self::$models['goods']->id,
            'client_id' => self::$models['client']->id,
            'weight'    => self::$models['goods_price']->weight,
            'comment'   => self::$models['client']->comment
        ]);

        $this->assertTrue($this->goodsOrder->existsOrder($order->client_id, $order->goods_id, $order->weight));

        $order->delete();

    }

    public function test_exists_goods()
    {
        $order = $this->goodsOrder->create([
            'goods_id'  => self::$models['goods']->id,
            'client_id' => self::$models['client']->id,
            'weight'    => self::$models['goods_price']->weight,
            'comment'   => self::$models['client']->comment
        ]);

        $this->assertTrue($this->goodsOrder->existsGoods($order->goods_id, $order->weight));

        $order->delete();
    }

    public function test_check_solvency()
    {
        $order = $this->goodsOrder->create([
            'goods_id'  => self::$models['goods']->id,
            'client_id' => self::$models['client']->id,
            'weight'    => self::$models['goods_price']->weight,
            'comment'   => self::$models['client']->comment
        ]);

        $goodsPrice = $this->goodsOrder->getGoodsPrice($order->id);

        self::$models['client']->update(['balance' => $goodsPrice->cost]);

        $this->assertInstanceOf(GoodsPrice::class, $this->goodsOrder->checkSolvency($order->id, self::$models['client']->id));

        $order->delete();
    }

    public function test_buy()
    {
        $order = $this->goodsOrder->create([
            'goods_id'  => self::$models['goods']->id,
            'client_id' => self::$models['client']->id,
            'weight'    => self::$models['goods_price']->weight,
            'comment'   => self::$models['client']->comment
        ]);

        $goodsPurchase = $this->goodsOrder->buy($order->id, $order->client_id);

        $goodsPurchase->delete();
        $this->cleanTable();
    }
}
