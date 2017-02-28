<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use App\Ghost\Repositories\Goods\GoodsReserve;
use App\Ghost\Repositories\Goods\GoodsManager;
use Carbon\Carbon;

class GoodsReserveTest extends TestCase
{
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
        $models = $this->testTools->clientWithOrder();

        $goodsReserve = $this->goodsReserve->makeReservation([
            'goods_price_id'    => $models->goods_price->id,
            'client_id'         => $models->client->id,
            'time'              => Carbon::now()->addDay(1)->toDateTimeString()
        ]);

        $this->assertGreaterThan(0, $this->goodsReserve->checkExpired($goodsReserve->id));
    }

    public function test_cancel_reserve()
    {
        $models = $this->testTools->clientWithOrder();

        $goodsReserve = $this->goodsReserve->makeReservation([
            'goods_price_id'    => $models->goods_price->id,
            'client_id'         => $models->client->id,
            'time'              => Carbon::now()->addDay(1)->toDateTimeString()
        ]);

        $this->goodsReserve->cancel($goodsReserve->id);
    }

    public function test_perform()
    {
        $models = $this->testTools->clientWithOrder();

        $goodsReserve = $this->goodsReserve->makeReservation([
            'goods_price_id'    => $models->goods_price->id,
            'client_id'         => $models->client->id,
            'time'              => Carbon::now()->addDay(1)->toDateTimeString()
        ]);

        $this->goodsReserve->perform($goodsReserve->id);
    }

    public function test_terminate()
    {
        $this->testTools->cleaningTemporaryRows();
    }
}
