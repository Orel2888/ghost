<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Container\Container;
use App\Admin;

class ApiAdminMethodsTest extends TestCase
{
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
        $this->cacheFlush();
    }

    public function cacheFlush()
    {
        Cache::flush();
    }
}
