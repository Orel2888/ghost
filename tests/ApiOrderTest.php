<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use App\{
    Client,
    Goods,
    GoodsOrder,
    GoodsPrice
};
use App\Ghost\Repositories\Goods\GoodsOrder as GoodsOrderRepo;
use Faker\Factory as Faker;

class ApiOrderTest extends TestCase
{
    use ApiTrait, BaseTestsHelper;

    /**
     * @var GoodsOrderRepo
     */
    public $goodsOrder;

    public function setUp()
    {
        parent::setUp();

        $this->goodsOrder = new GoodsOrderRepo();
    }

    public function test_order_create()
    {
        $accessToken = $this->authenticateUser();

        $faker = Faker::create();

        $clientName = $faker->name;

        $client = Client::create([
            'name'      => $clientName,
            'comment'   => $clientName
        ]);

        $goods = Goods::first();
        $goodsPrice = $goods->goodsPrice->first();

        // Create one a order
        $response = $this->call('POST', 'api/order.create', [
            'access_token'  => $accessToken,
            'goods_id'      => $goods->id,
            'weight'        => $goodsPrice->weight,
            'client_id'     => $client->id,
            'count'         => 1
        ]);

        $this->assertEquals(200, $response->getStatusCode());

        $responseData = json_decode($response->getContent())->data;

        $this->assertCount(1, $responseData->order_ids);

        GoodsOrder::find($responseData->order_ids[0])->delete();

        // Create a few order
        $response = $this->call('POST', 'api/order.create', [
            'access_token'  => $accessToken,
            'goods_id'      => $goods->id,
            'weight'        => $goodsPrice->weight,
            'client_id'     => $client->id,
            'count'         => 2
        ]);

        $this->assertEquals(200, $response->getStatusCode());

        $responseData = json_decode($response->getContent())->data;

        $this->assertCount(2, $responseData->order_ids);

        $client->delete();
    }

    public function test_create_order_and_buy()
    {
        $accessToken = $this->authenticateUser();

        $faker = Faker::create();

        $clientName = $faker->name;

        $client = Client::create([
            'name'      => $clientName,
            'comment'   => $clientName
        ]);

        $goods = Goods::first();
        $goodsPrice = $goods->goodsPrice->first();

        $client->update(['balance' => $goodsPrice->cost]);

        $response = $this->call('POST', 'api/order.create', [
            'access_token'  => $accessToken,
            'goods_id'      => $goods->id,
            'weight'        => $goodsPrice->weight,
            'count'         => 2,
            'client_id'     => $client->id
        ]);

        $this->assertEquals(200, $response->getStatusCode());

        $responseData = json_decode($response->getContent())->data;

        $this->assertEquals(1, $responseData->order_processed);

        $client->delete();

        GoodsPrice::create(array_only($goodsPrice->getAttributes(), [
            'goods_id',
            'miner_id',
            'weight',
            'address',
            'reserve',
            'cost'
        ]));
    }

    public function test_order_list()
    {
        $accessToken = $this->authenticateUser();

        $faker = Faker::create();

        $clientName = $faker->name;

        $client = Client::create([
            'name'      => $clientName,
            'comment'   => $clientName
        ]);

        $goods = Goods::first();
        $goodsPrice = $goods->goodsPrice->first();

        // Create two a order
        $response = $this->call('POST', 'api/order.create', [
            'access_token'  => $accessToken,
            'goods_id'      => $goods->id,
            'weight'        => $goodsPrice->weight,
            'client_id'     => $client->id,
            'count'         => 2
        ]);

        $this->assertEquals(200, $response->getStatusCode());

        $order_ids = json_decode($response->getContent())->data->order_ids;

        $response = $this->call('GET', 'api/order.list', [
            'access_token'  => $accessToken,
            'client_id'     => $client->id
        ]);

        $this->assertEquals(200, $response->getStatusCode());

        $responseData = json_decode($response->getContent())->data;
        //var_dump($responseData);
        $this->assertNotEmpty($responseData);

        $this->assertEquals($order_ids[0], $responseData[0]->id);
        $this->assertEquals($order_ids[1], $responseData[1]->id);

        $orderFirst = (array) $responseData[0];

        $this->assertArrayHasKey('id', $orderFirst);
        $this->assertArrayHasKey('city_name', $orderFirst);
        $this->assertArrayHasKey('goods_name', $orderFirst);
        $this->assertArrayHasKey('cost', $orderFirst);
        $this->assertArrayHasKey('weight', $orderFirst);
        $this->assertArrayHasKey('status', $orderFirst);
        $this->assertArrayHasKey('status_message', $orderFirst);
        $this->assertArrayHasKey('date', $orderFirst);

        $client->delete();
    }

    public function test_find_order()
    {
        $accessToken = $this->authenticateUser();

        $order = $this->createOrder();

        $response = $this->call('GET', 'api/order.find', [
            'access_token'  => $accessToken,
            'id'            => $order->id,
            'client_id'     => $order->client_id
        ]);

        $data = json_decode($response->getContent())->data;

        $this->assertNotEmpty($data->city_name);
        $this->assertNotEmpty($data->goods_name);
        $this->assertNotEmpty($data->weight);
        $this->assertNotEmpty($data->cost);
        $this->assertNotEmpty($data->id);
        $this->assertContains($data->status, [0, 1, 2, 3]);
        $this->assertNotEmpty($data->status_message);
        $this->assertNotEmpty($data->date);

        $order->client->delete();
    }

    public function test_order_deletes()
    {
        $accessToken = $this->authenticateUser();

        $order = $this->createOrder();

        $response = $this->call('POST', 'api/order.del', [
            'access_token'  => $accessToken,
            'client_id'     => $order->client->id,
            'order_id'      => $order->id
        ]);

        $this->assertEquals(200, $response->getStatusCode());

        $this->assertEquals('del', json_decode($response->getContent())->method);

        $order = $this->createOrder();

        $response = $this->call('POST', 'api/order.delall', [
            'access_token'  => $accessToken,
            'client_id'     => $order->client->id
        ]);

        $this->assertEquals(200, $response->getStatusCode());

        $this->assertEquals('delall', json_decode($response->getContent())->method);
    }

    public function test_end()
    {
        Cache::flush();
    }
}
