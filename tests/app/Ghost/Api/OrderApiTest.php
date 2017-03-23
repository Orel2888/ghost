<?php

use App\{
    GoodsPrice,
    Client,
    GoodsOrder,
    GoodsPurchase
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

    public function test_create_and_check_limit()
    {
        // Creating count orders exceeding the limit
        $orderLimit = config('shop.order_count_user');

        $client = Client::whereNotNull('comment')->first();

        $client->goodsOrders()->delete();

        $goodsFromPrice = GoodsPrice::first();

        $i = 1;
        while ($i <= ($orderLimit + 1)) {

            // Creating a orders
            $this->api->orderCreate([
                'goods_id'  => $goodsFromPrice->goods_id,
                'weight'    => $goodsFromPrice->weight,
                'count'     => 1,
                'client_id' => $client->id
            ], function ($responseData, $e) use ($i, $orderLimit) {
                BaseApi::throwException($e);

                if ($i == $orderLimit + 1) {
                    $this->assertEquals('fail', $responseData->status);
                }

                if (isset($responseData->data)) {
                    $this->testTools->storage->add('goods_orders', $responseData->data->order_ids[0]);
                }
            });

            $i++;
        }

        // ----------------------------------------------------------
        // Creating a purchases
        $client->goodsOrders()->delete();

        $productToPrice = $this->testTools->createGoodsToPrice($orderLimit + 2);

        $goodsFromPrice = $productToPrice[0];

        $client->update(['balance' => $goodsFromPrice->cost * count($productToPrice)]);

        $i = 1;
        while ($i <= ($orderLimit + 1)) {

            // Creating a orders
            $this->api->orderCreate([
                'goods_id'  => $goodsFromPrice->goods_id,
                'weight'    => $goodsFromPrice->weight,
                'count'     => 1,
                'client_id' => $client->id
            ], function ($responseData, $e) {
                BaseApi::throwException($e);

                $this->testTools->storage->add('goods_orders', $responseData->data->order_ids[0]);
            });

            $i++;
        }

        $this->assertEquals($orderLimit, GoodsOrder::whereClientId($client->id)->whereStatus(1)->count());

        $purchases = GoodsOrder::whereClientId($client->id)->where('purchase_id', '>', 0)->each(function ($order) {
            $this->testTools->storage->add('goods_purchases', $order->purchase_id);
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
        $models = $this->testTools->clientWithOrder(6);

        $models->order->take(3)->each(function ($item) {
            $item->update(['status' => 1]);
        });

        // Check get pending a orders
        $this->api->orderList($models->client->id, 'pending', function ($responseJson, $e) {
            BaseApi::throwException($e);

            $prevItem = null;
            foreach ($responseJson->data as $order) {
                $this->checkOrderResponse($order);

                $this->assertTrue(in_array($order->status, [0, 2, 3]));

                if (!is_null($prevItem)) {
                    $this->assertGreaterThan($prevItem->id, $order->id);
                }

                $prevItem = $order;
            }
        });

        // Check get successful a orders
        $this->api->orderList($models->client->id, 'successful', function ($responseJson, $e) {
            BaseApi::throwException($e);

            $prevItem = null;
            foreach ($responseJson->data as $order) {
                $this->checkOrderResponse($order);

                $this->assertEquals(1, $order->status);

                if (!is_null($prevItem)) {
                    $this->assertGreaterThan($prevItem->id, $order->id);
                }

                $prevItem = $order;
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