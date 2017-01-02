<?php

use App\{
    GoodsPrice,
    Client
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

        $this->api->run('order.create', [
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

        $this->api->run('order.create', [
            'goods_id'  => $goodsFromPrice->first()->goods_id,
            'weight'    => $goodsFromPrice->first()->weight,
            'count'     => 2,
            'client_id' => $client->id
        ], function ($responseData, $e) {
            BaseApi::throwException($e);

            $this->assertEquals(2, $responseData->data->count);

            foreach ($responseData->data->order_ids as $orderId) {
                $this->testTools->storage->add('goods_orders', $orderId);
            }
        });
    }

    public function test_create_and_purchase()
    {
        $client = Client::whereNotNull('comment')->first();

        $goodsFromPrice = GoodsPrice::first();

        $client->update(['balance' => $goodsFromPrice->cost]);

        $this->api->run('order.create', [
            'goods_id'  => $goodsFromPrice->goods_id,
            'weight'    => $goodsFromPrice->weight,
            'count'     => 1,
            'client_id' => $client->id
        ], function ($responseData, $e) {
            BaseApi::throwException($e);

            $this->assertNotEmpty($responseData->data);
            $this->assertEquals(1, $responseData->data->order_processed);

            $this->testTools->storage->add('goods_orders', $responseData->data->order_ids[0]);
        });
    }

    public function test_clear()
    {
        $this->testTools->cleaningTemporaryRows();
    }
}