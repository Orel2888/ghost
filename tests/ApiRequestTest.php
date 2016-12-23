<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class ApiRequestTest extends TestCase
{
    protected static $container = null;

    public function setUp()
    {
        parent::setUp();

        if (is_null(static::$container))  {
            static::$container = [];
        }
    }

    public function test_api_request()
    {
        $response = $this->call('GET', 'api/users.find');

        $this->assertEquals(403, $response->getStatusCode());
    }

    public function test_api_authenticate()
    {
        $response = $this->call('POST', 'api/authenticate');

        $this->assertEquals(400, $response->getStatusCode());

        /*$response = $this->call('GET', 'api/users.find', ['access_token' => 123]);

        $this->assertEquals(401, $response->getStatusCode());*/

        $response = $this->call('POST', 'api/authenticate', ['key' => 123]);
        //echo $response->getContent();
        $this->assertEquals(401, $response->getStatusCode());

        $response = $this->call('POST', 'api/authenticate', ['key' => env('API_KEY')]);
        //echo $response->getContent();
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertNotEmpty($response->getContent());

        $authData = json_decode($response->getContent());

        static::$container['access_token'] = $authData->access_token;
    }

    public function test_request_to_method()
    {
        $response = $this->call('GET', 'api/users.find', ['access_token' => static::$container['access_token']]);

        var_dump($response->getStatusCode(), $response->getContent());

        Cache::flush();
    }
}
