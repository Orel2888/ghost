<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use App\Ghost\Repositories\Goods\GoodsManager;
use Carbon\Carbon;
use App\{
    City,
    Client,
    Miner
};

class GoodsManagerTest extends TestCase
{

    protected $databaseSeed = true;

    /**
     * @var GoodsManager
     */
    public $goodsManager;

    public $countGoodsPriceType = 2;

    public function setUp()
    {
        parent::setUp();

        $this->goodsManager = new GoodsManager();
    }

    public function test_find_city()
    {
        $city = City::first();

        $this->goodsManager->findCity($city->id);
        $this->goodsManager->findCity($city->name);
    }

    public function test_add_goods()
    {
        $city = City::first();

        $goods = $this->goodsManager->addGoods([
            'goods_name'    => 'Апельсины',
            'city_id'       => $city->id
        ]);
    }

    public function test_find_goods()
    {
        $city = City::first();

        $goods = $this->goodsManager->addGoods([
            'goods_name'    => 'Апельсин',
            'city_id'       => $city->id
        ]);

        $this->goodsManager->findGoods($goods->id);
    }

    public function test_add_goods_price()
    {
        $city   = City::first();
        $client = Client::first();
        $miner  = Miner::first();

        $goods = $this->goodsManager->addGoods([
            'goods_name'    => 'Апельсины',
            'city_id'       => $city->id
        ]);

        $goodsData = [
            'miner_id'  => $miner->id,
            'goods_id'  => $goods->id,
            'weight'    => '0.5',
            'address'   => 'The backup and restore operations for global variables and static',
            'cost'      => 1000
        ];

        $goodsPrice = $this->goodsManager->addGoodsPrice($goodsData);

        $goodsPriceReserved = $this->goodsManager->addGoodsPrice($goodsData, [
            'client_id' => $client->id,
            'time'      => Carbon::now()->addDay(1)->toDateTimeString()
        ]);

        $this->assertGreaterThan(0, Miner::find($miner->id)->counter_goods);
    }

    public function test_parse_addresses()
    {
        $list = sprintf('1) The text%s2) The text%s3) The text', PHP_EOL, PHP_EOL);

        $matches = $this->goodsManager->parseAddresses($list);

        $this->assertNotEmpty($matches);
    }

    public function test_goods_weights_and_count()
    {
        $city = City::first();

        $goods = $this->goodsManager->goods->where('city_id', $city->id)->first();

        $this->assertEquals($this->countGoodsPriceType, count($this->goodsManager->getGoodsWeightsAndCount($goods->id)));

        $goodsWeights = $this->goodsManager->getGoodsWeightsAndCount($goods->id, ['cost']);

        foreach ($goodsWeights as $weight => $attributes) {
            $this->assertArrayHasKey('count', $goodsWeights[$weight]);
            $this->assertArrayHasKey('cost', $goodsWeights[$weight]);
        }
    }

    public function test_goods_price_list()
    {
        $priceList = $this->goodsManager->getPriceList();

        $this->assertNotEmpty($priceList);

        $this->assertNotEmpty(isset($priceList));
    }

    public function test_goods_check_exists()
    {
        $goods = $this->goodsManager->goods->first();

        $minerId = Miner::first()->id;

        $randomWeight = '0.'. mt_rand(1, 99);

        $goodsPrices = [];

        $i = 0;
        while ($i < 2) {
            $goodsPrices[] = $this->goodsManager->addGoodsPrice([
                'goods_id'  => $goods->id,
                'miner_id'  => $minerId,
                'weight'    => $randomWeight,
                'address'   => 'Test',
                'cost'      => 1005
            ]);
            $i++;
        }

        $this->assertTrue($this->goodsManager->goodsPriceCheckExists($goods->id, $randomWeight, 2));

        foreach ($goodsPrices as $goodsPrice) {
            $goodsPrice->delete();
        }
    }
}