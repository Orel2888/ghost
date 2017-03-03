<?php

use App\{
    Client,
    Admin,
    Purse
};

class SystemApiTest extends TestCase
{
    /**
     * @var SystemApi
     */
    public $api;

    public function setUp()
    {
        parent::setUp();

        $this->api = (new SystemApi())->saveToken();
    }

    public function test_authenticate_admin()
    {
        $this->api->authentication(Admin::first()->login);
    }

    public function test_processing_goods_orders_by_balances()
    {
        // Perform a processing orders of clients for balance accounts
        $models = $this->testTools->clientWithOrder();

        $models->client->update([
            'balance'           => $models->goods_price->cost,
            'count_purchases'   => 0
        ]);

        $this->api->sysProcessingGoodsOrders(function ($responseJson, $e) use($models) {
            BaseApi::throwException($e);

            $this->assertCount(1, $responseJson->data->orders_ids_successful);
            $this->assertCount(1, $responseJson->data->purchases_ids);

            $this->assertEquals(1, Client::find($models->client->id)->count_purchases);

            // Collect a trash
            $this->testTools->storage->add('goods_purchases', $responseJson->data->purchases_ids[0]);
        });

        $models->client->update(['count_purchases' => 0]);

    }

    public function test_processing_goods_orders_by_transactions()
    {
        // Perform a processing orders for transactions
        $models = $this->testTools->clientWithOrder();

        // Create a transaction
        $transaction = $this->testTools->createTransaction($models->goods_price->cost, $models->client->comment);

        $this->api->sysProcessingGoodsOrders(function ($responseJson, $e) {
            BaseApi::throwException($e);

            $this->assertEquals(1, $responseJson->data->number_successfull_trans);
            $this->assertCount(1, $responseJson->data->orders_ids_successful);
            $this->assertCount(1, $responseJson->data->client_ids_updated_balance);
            $this->assertCount(1, $responseJson->data->purchases_ids);

            // Collect a trash
            $this->testTools->storage->add('goods_purchases', $responseJson->data->purchases_ids[0]);
        });
    }

    public function test_purse_update_balance()
    {
        $purse = Purse::first();

        $this->api->sysPurseUpdateBalance(['phone' => $purse->phone, 'balance' => 22], function ($responseJson, $e) {
            BaseApi::throwException($e);

        });

        $this->assertEquals(22, Purse::find($purse->id)->balance);
    }

    public function test_clear()
    {
        $this->testTools->cleaningTemporaryRows();
    }
}