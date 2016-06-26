<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Container\Container;
use App\Admin;

class ApiAdminMethodsTest extends TestCase
{
    use BaseTestsHelper;

    /**
     * @var Container
     */
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
        $admin = Admin::create([
            'login' => 'Vasechka'
        ]);

        $response = $this->call('POST', 'api/authenticate/'. $admin->login, ['key' => env('API_KEY')]);

        $this->assertEquals(200, $response->getStatusCode());

        static::$container['access_token'] = json_decode($response->getContent())->access_token;

        $admin->delete();
    }

    public function test_qiwi_transaction()
    {
        $response = $this->call('GET', 'api/admin/qiwi-transaction', ['access_token' => static::$container['access_token']]);

        $this->assertEquals(200, $response->getStatusCode());

        $receivedData = json_decode($response->getContent());

        $this->assertNotEmpty($receivedData->data);
    }

    public function test_goods_price()
    {
        $this->createData();

        $response = $this->call('GET', 'api/admin/goods-price', ['access_token' => static::$container['access_token']]);

        $this->assertEquals(200, $response->getStatusCode());

        $responseData = json_decode($response->getContent())->data;

        $this->assertNotEmpty($responseData);

        var_dump($responseData);

        $this->removeData();
    }

    public function test_end()
    {
        $this->cacheFlush();
    }

    public function cacheFlush()
    {
        Cache::flush();
    }
}
