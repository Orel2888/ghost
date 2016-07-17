<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Container\Container;

class ApiGoodsTest extends TestCase
{
    protected static $container;

    public function setUp()
    {
        parent::setUp();

        if (is_null(static::$container)) {
            static::$container = new Container();
        }
    }

    public function test_authenticate()
    {
        $response = $this->call('POST', 'api/authenticate', ['key' => env('API_KEY')]);

        $this->assertEquals(200, $response->getStatusCode());

        static::$container['access_token'] = json_decode($response->getContent())->access_token;
    }

    public function test_goods_price_list()
    {
        $response = $this->call('GET', 'api/goods.pricelist', [
            'access_token'  => static::$container['access_token']
        ]);

        $this->assertEquals(200, $response->getStatusCode());

        $responseData = json_decode($response->getContent());

        $this->assertNotEmpty($responseData->data);
    }

    public function test_end()
    {
        Cache::flush();
    }
}
