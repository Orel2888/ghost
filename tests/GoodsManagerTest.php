<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

use App\Ghost\Repositories\Goods\GoodsManager;
use App\City;
use App\Miner;
use App\Client;
use Carbon\Carbon;
use App\Ghost\Libs\GibberishAES;

class GoodsManagerTest extends TestCase
{
    use BaseTestsHelper;

    /**
     * @var GoodsManager
     */
    private $goodsManager;

    public function setUp()
    {
        parent::setUp();

        $this->goodsManager = new GoodsManager();
    }

    public function test_find_city()
    {
        $city = $this->createCity();

        $this->goodsManager->findCity($city->id);
        $this->goodsManager->findCity($city->name);

        $city->delete();
    }

    public function test_add_goods()
    {
        $city = $this->createCity();

        $goods = $this->goodsManager->addGoods([
            'goods_name'    => 'Апельсины',
            'city_id'       => $city->id
        ]);

        $goods->delete();
        $city->delete();
    }

    public function test_find_goods()
    {
        $city = $this->createCity('Новосибирск');

        $goods = $this->goodsManager->addGoods([
            'goods_name'    => 'Апельсин',
            'city_id'       => $city->id
        ]);

        $this->goodsManager->findGoods($goods->id);

        $city->delete();
    }

    public function test_add_goods_price()
    {
        $city = $this->createCity();
        $client = $this->createClient();

        $miner = Miner::create([
            'name'  => 'Vadim'
        ]);

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

        $this->assertGreaterThan(0, Miner::find($miner->id)->count_goods);

        $city->delete();
        $miner->delete();
        $goods->delete();
        $goodsPrice->delete();
        $goodsPriceReserved->delete();
    }

    public function test_parse_addresses()
    {
        $list = sprintf('1) The text%s2) The text%s3) The text', PHP_EOL, PHP_EOL);

        $matches = $this->goodsManager->parseAddresses($list);

        $this->assertNotEmpty($matches);
    }

    public function test_goods_weights_and_count()
    {
        $city = $this->goodsManager->findCity('Светлоград');

        $goods = $this->goodsManager->goods->where('city_id', $city->id)->first();

        $this->assertEquals(3, count($this->goodsManager->getGoodsWeightsAndCount($goods->id)));

        $goodsWeights = $this->goodsManager->getGoodsWeightsAndCount($goods->id, ['cost']);

        $this->assertArrayHasKey('count', $goodsWeights['0.2']);
        $this->assertArrayHasKey('cost', $goodsWeights['0.2']);
    }

    public function test_goods_price_list()
    {
        $priceList = $this->goodsManager->getPriceList();

        $this->assertNotEmpty($priceList);

        $city = 'Буденновск';
        $goodsType = 'Тв';
        $weight = '3';

        $this->assertTrue(isset($priceList[$city]));
        $this->assertTrue(isset($priceList[$city][$goodsType]));
        $this->assertTrue(isset($priceList[$city][$goodsType][$weight]));
        $this->assertTrue(isset($priceList[$city][$goodsType][$weight]['goods_id']));
        $this->assertTrue(isset($priceList[$city][$goodsType][$weight]['cost']));
        $this->assertTrue(isset($priceList[$city][$goodsType][$weight]['count']));
    }

    public function test_goods_check_exists()
    {
        $goods = $this->goodsManager->goods->first();

        $this->assertTrue($this->goodsManager->goodsPriceCheckExists($goods->id, 0.2, 2));
    }

    public function test_get_goods_type_by_weight()
    {
        $goods = $this->goodsManager->goods->first();

        $this->assertCount(2, $this->goodsManager->getGoodsPriceByWeight($goods->id, 0.2, 2));
    }
}
