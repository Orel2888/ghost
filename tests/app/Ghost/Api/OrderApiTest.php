<?php

use App\{
    GoodsPrice,
    Client,
    GoodsOrder
};

class OrderApiTest extends TestCase
{
    /**
     * @var OrderApi
     */
    public $api;

    public function setUp()
    {
        parent::setUp();

        $this->api = new OrderApi();

        $this->api->saveToken();
    }

    public function test_authenticate()
    {
        $this->api->authentication();
    }

    public function test_create()
    {
        $client = Client::whereNotNull('comment')->first();

        // Create one a order
        $goodsFromPrice = GoodsPrice::first();

        $this->api->orderCreate([
            'goods_id'  => $goodsFromPrice->goods_id,
            'weight'    => $goodsFromPrice->weight,
            'count'     => 1,
            'client_id' => $client->id
        ], function ($responseData, $e) {
            BaseApi::throwException($e);

            $this->assertNotEmpty($responseData->data);

            $this->testTools->storage->add('goods_orders', $responseData->data->order_ids[0]);
        });

        // Create a few orders
        $goodsFromPrice = $this->testTools->createGoodsToPrice(2);

        $this->api->orderCreate([
            'goods_id'  => $goodsFromPrice->first()->goods_id,
            'weight'    => $goodsFromPrice->first()->weight,
            'count'     => 2,
            'client_id' => $client->id
        ], function ($responseData, $e) {
            BaseApi::throwException($e);

            $this->assertEquals(2, $responseData->data->count);

            foreach ($responseData->data->order_ids as $orderId) {
                $this->testTools->storage->add('goods_orders', $orderId);
                $this->testTools->storage->add('goods_purchases', GoodsOrder::find($orderId)->purchase_id);
            }
        });
    }

    public function test_create_and_purchase()
    {
        $client = Client::whereNotNull('comment')->first();

        $goodsFromPrice = GoodsPrice::first();

        $client->update(['balance' => $goodsFromPrice->cost]);

        $this->api->orderCreate([
            'goods_id'  => $goodsFromPrice->goods_id,
            'weight'    => $goodsFromPrice->weight,
            'count'     => 1,
            'client_id' => $client->id
        ], function ($responseData, $e) {
            BaseApi::throwException($e);

            $this->assertNotEmpty($responseData->data);
            $this->assertEquals(1, $responseData->data->order_processed);

            $this->testTools->storage->add('goods_orders', $responseData->data->order_ids[0]);
            $this->testTools->storage->add('goods_purchases', GoodsOrder::find($responseData->data->order_ids[0])->purchase_id);
        });
    }

    public function test_find()
    {
        $models = $this->testTools->clientWithOrder();

        $this->api->orderFind(['id' => $models->order->id, 'client_id' => $models->client->id], function ($responseJson, $e) use($models) {
            BaseApi::throwException($e);

            $this->assertEquals($responseJson->data->id, $models->order->id);

            $this->checkOrderResponse($responseJson->data);
        });
    }

    public function test_list()
    {
        $models = $this->testTools->clientWithOrder();
        
        $this->api->orderList($models->client->id, function ($responseJson, $e) {
            BaseApi::throwException($e);
            
            foreach ($responseJson->data as $order) {
                $this->checkOrderResponse($order);
            }
        });
    }

    public function test_del()
    {
        $models = $this->testTools->clientWithOrder();

        $this->api->orderDel(['order_id' => $models->order->id, 'client_id' => $models->client->id], function ($responseJson, $e) {
            BaseApi::throwException($e);

            $this->assertEquals('del', $responseJson->method);
        });
    }

    public function test_delall()
    {
        $models = $this->testTools->clientWithOrder();

        $this->api->orderDelAll($models->client->id, function ($responseJson, $e) {
            BaseApi::throwException($e);

            $this->assertEquals('delall', $responseJson->method);
        });
    }

    /**
     * @param $order
     */
    public function checkOrderResponse($order)
    {
        $this->assertNotEmpty($order->city_name);
        $this->assertNotEmpty($order->goods_name);
        $this->assertNotEmpty($order->weight);
        $this->assertNotEmpty($order->cost);
        $this->assertTrue(is_numeric($order->status));
        $this->assertNotEmpty($order->status_message);
        $this->assertNotEmpty($order->date);
    }

    public function test_clear()
    {
        $this->testTools->cleaningTemporaryRows();
    }
}