<?php

class AuthenticationTest extends TestCase
{
    /**
     * @var ApiClient
     */
    public $apiClient;

    public function setUp()
    {
        parent::setUp();

        $this->apiClient = new ApiClient();
    }

    public function test_common_authenticate()
    {
        $request = $this->apiClient->authentication();

        $this->assertEquals(200, $request->getStatusCode());

        $this->assertNotNull($this->apiClient->getToken());
    }

    public function test_admin_authenticate()
    {
        $request = $this->apiClient->authentication(App\Admin::first()->login);

        $this->assertEquals(200, $request->getStatusCode());
    }

    public function test_check_access_token()
    {
        $request = $this->apiClient->authentication();

        $this->assertEquals(200, $this->apiClient->authenticateToken()->getStatusCode());
    }

    public function test_fail_check_access_token()
    {
        try {
            $this->apiClient->authenticateToken('wrong');
        } catch (GuzzleHttp\Exception\ClientException $e) {
            $this->assertEquals(401, $e->getCode());
        }
    }

    public function test_fails_authentication()
    {
        try {
            $this->apiClient->authentication(null, true);
        } catch (GuzzleHttp\Exception\ClientException $e) {
            $this->assertEquals(400, $e->getCode());
        }

        $this->apiClient->setApiKey('wrong');
        
        try {
            $this->apiClient->authentication();
        } catch (GuzzleHttp\Exception\ClientException $e) {
            $this->assertEquals(401, $e->getCode());
        }
    }
}