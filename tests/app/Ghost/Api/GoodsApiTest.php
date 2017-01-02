<?php

class GoodsApiTest extends TestCase
{
    /**
     * @var GoodsApi
     */
    public $api;

    public function setUp()
    {
        parent::setUp();

        $this->api = new GoodsApi();

        $this->api->saveToken();
    }

    public function test_authenticate()
    {
        $this->api->authentication();
    }

    public function test_goods_pricelist()
    {
        $this->api->run('goods.pricelist', null, function ($responseData, $e) {
            BaseApi::throwException($e);

            //dump($responseData);

            $this->assertNotEmpty($responseData->data);
        });
    }
}