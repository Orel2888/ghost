<?php

trait ApiTrait
{
    public function authenticateUser()
    {
        $response = $this->call('POST', 'api/authenticate', ['key' => env('API_KEY')]);

        $this->assertEquals(200, $response->getStatusCode());

        return json_decode($response->getContent())->access_token;
    }
}
