<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Container\Container;
use App\Purse;
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
        $response = $this->call('POST', 'api/authenticate/some', ['key' => env('API_KEY')]);

        $this->assertEquals(401, $response->getStatusCode());

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
        // var_dump($receivedData);
        $this->assertNotEmpty($receivedData->data);
    }

    public function test_goods_price()
    {
        $this->createData();

        $response = $this->call('GET', 'api/admin/goods-price', ['access_token' => static::$container['access_token']]);

        $this->assertEquals(200, $response->getStatusCode());

        $responseData = json_decode($response->getContent())->data;

        $this->assertNotEmpty($responseData);

        //var_dump($responseData);

        $this->removeData();
    }

    public function test_goods_purchase()
    {
        $this->createData();

        $response = $this->call('POST', 'api/admin/goods-price/purchase', [
            'access_token'      => static::$container['access_token'],
            'goods_price_id'    => static::$database['goods_price'][0]->id
        ]);
        
        $this->assertEquals(200, $response->getStatusCode());

        $someGoodsPrice = array_map(function ($item) {
            return $item->id;
        }, array_slice(static::$database['goods_price'], 0, 3));

        $response = $this->call('POST', 'api/admin/goods-price/purchase', [
            'access_token'      => static::$container['access_token'],
            'goods_price_id'    => implode(',', $someGoodsPrice)
        ]);

        $this->removeData();
    }

    public function test_goods_price_available()
    {
        $this->createData();

        $response = $this->call('GET', 'api/admin/goods-price/available', [
            'access_token'      => static::$container['access_token']
        ]);

        $this->assertEquals(200, $response->getStatusCode());

        $this->assertNotEmpty(json_decode($response->getContent())->data);

        $this->removeData();
    }

    public function test_get_purse()
    {
        $purse = Purse::create([
            'phone' => 79887587475,
            'pass'  => '2ws2ws',
            'balance'   => 1
        ]);

        $response = $this->call('GET', 'api/admin/purse', [
            'access_token'      => static::$container['access_token']
        ]);

        $this->assertEquals(200, $response->getStatusCode());

        $responseData = json_decode($response->getContent())->data;

        $this->assertNotEmpty($responseData);

        $this->assertEquals($purse->phone, $responseData[0]->phone);
        $this->assertEquals($purse->pass, $responseData[0]->pass);
        $this->assertEquals($purse->balance, $responseData[0]->balance);

        static::$container['purse_id'] = $purse->id;
    }

    public function test_purse_set()
    {
        $response = $this->call('POST', 'api/admin/purse/set', [
            'access_token'  => static::$container['access_token'],
            'id'    => static::$container['purse_id']
        ]);
        
        $this->assertEquals(200, $response->getStatusCode());

        Purse::find(static::$container['purse_id'])->delete();
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
