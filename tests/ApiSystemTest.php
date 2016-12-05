<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use App\QiwiTransaction;
use App\Client;
use App\Ghost\Repositories\Goods\GoodsOrder;
use App\Goods;
use App\GoodsPrice;
use App\Purse;
use Faker\Factory as FakerFactory;

class ApiSystemTest extends TestCase
{
    use ApiTrait;

    public $modelQiwiTransaction;

    public $modelClient;

    public $modelGoods;

    public $goodsOrder;

    public $goodsPrice;

    public function setUp()
    {
        parent::setUp();

        $this->modelQiwiTransaction = new QiwiTransaction();
        $this->modelClient = new Client();
        $this->modelGoods  = new Goods();
        $this->goodsOrder  = new GoodsOrder();
        $this->goodsPrice  = new GoodsPrice();
    }

    public function test_processing_goods_orders()
    {
        $faker = FakerFactory::create();

        $clientName = $faker->name;

        $goods = $this->modelGoods->first();
        $someoneGoodsPrice = $this->goodsPrice->whereGoodsId($goods->id)->first();

        $client = $this->modelClient->create([
            'name'      => $clientName,
            'comment'   => $clientName
        ]);

        $order = $this->goodsOrder->create([
            'goods_id'  => $goods->id,
            'client_id' => $client->id,
            'comment'   => $client->comment,
            'weight'    => $someoneGoodsPrice->weight,
            'cost'      => $someoneGoodsPrice->cost
        ]);

        $transaction = $this->modelQiwiTransaction->create([
            'qiwi_id'   => 123,
            'amount'    => $someoneGoodsPrice->cost,
            'comment'   => $client->comment
        ]);

        $response = $this->call('POST', 'api/authenticate/Ghost228', [
            'key'   => env('API_KEY')
        ]);

        //echo $response->getContent();
        $this->assertEquals(200, $response->getStatusCode());

        $accessToken = json_decode($response->getContent())->access_token;

        $response = $this->call('GET', 'api/sys.processing_goods_orders', [
            'access_token'  => $accessToken
        ]);
        //echo $response->getContent();
        $this->assertEquals(200, $response->getStatusCode());

        $responseData = json_decode($response->getContent())->data;

        //var_dump($responseData);
        $this->assertCount(1, $responseData->client_ids_updated_balance);
        $this->assertCount(1, $responseData->orders_ids_successful);
        $this->assertEquals(1, $responseData->number_successfull_trans);

        $client->delete();
        $transaction->delete();
    }

    public function test_purse_update_balance()
    {
        $accessToken = $this->authenticateAdmin();

        $purse = Purse::create(['phone' => 79881111111]);

        $response = $this->call('POST', 'api/sys.purse_update_balance', [
            'access_token'  => $accessToken,
            'phone'         => $purse->phone,
            'balance'       => 1
        ]);

        $this->assertEquals(200, $response->getStatusCode());

        $purse->delete();
    }

    public function test_end()
    {
        Cache::flush();
    }
}
