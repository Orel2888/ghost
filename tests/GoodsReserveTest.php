<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use App\Ghost\Repositories\Goods\GoodsReserve;
use App\Ghost\Repositories\Goods\GoodsManager;
use Carbon\Carbon;

class GoodsReserveTest extends TestCase
{
    use BaseTestsHelper;

    /**
     * @var GoodsReserve
     */
    private $goodsReserve;

    /**
     * @var GoodsManager
     */
    private $goodsManager;

    public function setUp()
    {
        parent::setUp();

        $this->goodsReserve = new GoodsReserve();
        $this->goodsManager = new GoodsManager();
    }

    public function test_check_expired()
    {
        $city  = $this->createMiner();
        $goods = $this->createGoods();

        $goodsPrice = $this->goodsManager->addGoodsPrice([
            'goods_id'  => $goods->id,
            'city_id'   => $goods->city_id,
            'miner_id'  => $city->id,
            'weight'    => 0.5,
            'address'   => 'Prospect costa 277',
            'cost'      => 1000
        ]);

        $goodsReserve = $this->goodsReserve->makeReservation([
            'goods_price_id'    => $goodsPrice->id,
            'client_id'         => $this->createClient()->id,
            'time'              => Carbon::now()->addDay(1)->toDateTimeString()
        ]);

        $this->assertGreaterThan(0, $this->goodsReserve->checkExpired($goodsReserve->id));

        $goodsReserve->delete();
        $goodsPrice->delete();
    }

    public function test_cancel_reserve()
    {
        $goodsPrice = $this->createGoodsPrice();

        $goodsReserve = $this->goodsReserve->makeReservation([
            'goods_price_id'    => $goodsPrice->id,
            'client_id'         => $this->createClient()->id,
            'time'              => Carbon::now()->addDay(1)->toDateTimeString()
        ]);

        $this->goodsReserve->cancel($goodsReserve->id);

        $goodsPrice->delete();
    }

    public function test_perform()
    {
        $goodsPrice = $this->createGoodsPrice();

        $goodsReserve = $this->goodsReserve->makeReservation([
            'goods_price_id'    => $goodsPrice->id,
            'client_id'         => $this->createClient()->id,
            'time'              => Carbon::now()->addDay(1)->toDateTimeString()
        ]);

        $this->goodsReserve->perform($goodsReserve->id);

        $goodsPrice->delete();
    }
}
