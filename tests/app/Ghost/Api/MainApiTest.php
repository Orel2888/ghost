<?php

use App\Client;

class MainApiTest extends TestCase
{
    /**
     * @var MainApi
     */
    public $api;

    public function setUp()
    {
        parent::setUp();

        $this->api = new MainApi();
    }

    public function test_common_authenticate()
    {
        $response = $this->api->authentication();

        $this->assertEquals(200, $response->getStatusCode());

        $this->assertNotNull($this->api->getToken());
    }

    public function test_admin_authenticate()
    {
        $request = $this->api->authentication(App\Admin::first()->login);

        $this->assertEquals(200, $request->getStatusCode());
    }

    public function test_check_access_token()
    {
        $request = $this->api->authentication();

        $this->assertEquals(200, $this->api->authenticateToken()->getStatusCode());
    }

    public function test_fail_check_access_token()
    {
        try {
            $this->api->authenticateToken('wrong');
        } catch (GuzzleHttp\Exception\ClientException $e) {
            $this->assertEquals(401, $e->getCode());
        }
    }

    public function test_fails_authentication()
    {
        try {
            $this->api->authentication(null, true);
        } catch (GuzzleHttp\Exception\ClientException $e) {
            $this->assertEquals(400, $e->getCode());
        }

        $this->api->setApiKey('wrong');
        
        try {
            $this->api->authentication();
        } catch (GuzzleHttp\Exception\ClientException $e) {
            $this->assertEquals(401, $e->getCode());
        }
    }

    public function test_save_token()
    {
        $this->api->saveToken()->authentication();
    }

    public function test_restore_token()
    {
        $this->assertNotNull($this->api->restoreToken());

        $this->assertNotNull($this->api->getToken());
    }

    public function test_remove_token_store()
    {
        $this->assertTrue($this->api->removeTokenStore());
    }

    public function test_bad_request()
    {
        $this->api->makeBadRequest(function (\GuzzleHttp\Psr7\Response $response, $jsonData) {
            //dump($response->getStatusCode(), $jsonData);

            $this->assertArrayHasKey('exception', (array) $jsonData);
        });
    }

    public function test_running_api_requests()
    {
        $this->api = new UsersApi();

        $this->api->authentication();

        // Simulate bad request to method users.find
        $this->api->run('users.find', 'GET', ['tg_chatid' => 111222111], function ($responseJson, $e) {
            if ($e->getStatusCode() != 404) {
                BaseApi::throwException($e);
            }

            $this->assertEquals(404, $e->getStatusCode());
        });

        // Normal request to method users.find
        $this->api->run('users.find', 'GET', ['tg_chatid' => Client::first()->first()->tg_chatid], function ($responseJson, $e) {
            BaseApi::throwException($e);

            $this->assertNotEmpty($responseJson);
        });
    }
}