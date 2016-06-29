<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Container\Container;
use App\Client;
use App\Admin;

class ApiMethodsTest extends TestCase
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

    public function test_check_access_token()
    {
        $response = $this->call('POST', 'api/authenticate/check-access-token', [
            'access_token'  => static::$container['access_token']
        ]);

        $this->assertEquals(200, $response->getStatusCode());

        $this->assertEquals('ok', json_decode($response->getContent())->status);
    }

    public function test_users_find_not_found()
    {
        $response = $this->call('GET', 'api/users.find', [
            'tg_chatid'     => 123,
            'access_token'  => static::$container['access_token']
        ]);

        $this->assertEquals(404, $response->getStatusCode());
    }

    public function test_users_reg()
    {
        $response = $this->call('POST', 'api/users.reg?access_token='. static::$container['access_token'], [
            'name'          => 'Vano Vano',
            'tg_username'   => 'username',
            'tg_chatid'     => 123,
        ]);

        $this->assertEquals(201, $response->getStatusCode());

        $client_id = json_decode($response->getContent())->client_id;

        $this->assertGreaterThan(0, $client_id);

        Client::find($client_id)->delete();

        $this->cacheFlush();
    }

    public function cacheFlush()
    {
        Cache::flush();
    }
}
