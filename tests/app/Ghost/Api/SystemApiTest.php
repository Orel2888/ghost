<?php

use App\{
    Client,
    Admin,
    Purse,
    BlackListUser,
    QiwiTransaction
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

    public function test_processing_goods_orders_blacklist()
    {
        // Create black list
        $blacklistUsername = 'test_test';
        $blacklistPhone    = '79111113112';

        $black1 = BlackListUser::create([
            'username'  => $blacklistUsername,
            'type'      => 'username'
        ]);
        $black2 = BlackListUser::create([
            'phone'     => $blacklistPhone,
            'type'      => 'phone'
        ]);
        $this->testTools->storage->add('black_list_users', $black1->id);
        $this->testTools->storage->add('black_list_users', $black2->id);

        // Create a transactions
        $transAttr = [
            'purse'     => 79123343434,
            'qiwi_id'   => 1233332222444,
            'amount'    => 1500,
            'provider'  => 'Provider'
        ];

        $tr1 = QiwiTransaction::create($transAttr + [
            'comment'   => $blacklistUsername,
        ]);

        $tr2 = QiwiTransaction::create(array_merge($transAttr, [
            'provider'  => 'Provider provider +'. $blacklistPhone .' provider'
        ]));

        $this->testTools->storage->add('qiwi_transactions', $tr1->id);
        $this->testTools->storage->add('qiwi_transactions', $tr2->id);

        // Start test
        $this->api->sysProcessingGoodsOrders(function ($responseJson, $e) {
            BaseApi::throwException($e);

            $this->assertCount(2, $responseJson->data->transactions_ids_blacklist);
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