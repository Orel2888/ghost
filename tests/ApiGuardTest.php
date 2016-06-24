<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use App\Ghost\Api\ApiGuard;

class ApiGuardTest extends TestCase
{
    /**
     * @var ApiGuard
     */
    protected $apiGuard;

    public function setUp()
    {
        parent::setUp();

        $this->apiGuard = new ApiGuard();
    }

    public function test_authenticate()
    {
        $authenticate = $this->apiGuard->authenticate(env('API_KEY'));

        $this->assertNotFalse($authenticate);

        $this->assertTrue($this->apiGuard->hasAccessToken($authenticate['access_token']));

        $this->assertNotEmpty($this->apiGuard->getInfoAccessToken($authenticate['access_token']));
    }
}
