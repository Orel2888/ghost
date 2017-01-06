<?php

use App\{
    GoodsPrice,
    GoodsPurchase,
    Purse,
    Admin
};

class AdminApiTest extends TestCase
{
    /**
     * @var AdminApi
     */
    public $api;

    public function setUp()
    {
        parent::setUp();

        $this->api = (new AdminApi())->saveToken();
    }

    public function test_authenticate()
    {
        $this->api->authentication(Admin::first()->login);
    }

    public function test_qiwi_transaction()
    {
        $this->api->qiwiTransaction(function ($responseJson, $e) {
            BaseApi::throwException($e);

            $this->assertNotEmpty($responseJson->data);
        });
    }

    public function test_product_purchase()
    {
        $product = $this->testTools->createGoodsToPrice()->first();

        $this->api->productPurchase(['goods_price_id' => $product->id], function ($responseJson, $e) use ($product) {
            BaseApi::throwException($e);

            $this->assertNull(GoodsPrice::find($product->id));

            $purchaseId = $responseJson->data[0]->id;

            $this->assertNotNull(GoodsPurchase::find($purchaseId));

            $this->testTools->storage->add('goods_purchases', $purchaseId);
        });
    }

    public function test_available()
    {
        $this->api->productAvailable(function ($responseJson, $e) {
            BaseApi::throwException($e);

            $this->assertNotEmpty($responseJson->data);
        });
    }

    public function test_list()
    {
        $this->api->productList(function ($responseJson, $e) {
            BaseApi::throwException($e);

            $this->assertNotEmpty($responseJson->data);
        });
    }

    public function test_purse()
    {
        $this->api->purse(function ($responseJson, $e) {
            BaseApi::throwException($e);

            $this->assertNotEmpty($responseJson->data);
        });
    }

    public function test_purse_set()
    {
        $this->api->purseSet(Purse::first()->id, function ($responseJson, $e) {
            BaseApi::throwException($e);

        });
    }

    public function test_clear()
    {
        $this->testTools->cleaningTemporaryRows();
    }
}