<?php

use GuzzleHttp\Client;

class ApiClient
{
    /**
     * @var Client
     */
    private $http;
    
    private $apiKey;

    private $accessToken;

    private $dataCapture = false;

    public function __construct()
    {
        $this->http    = new Client(['base_uri'  => env('API_URL') .'/']);
        $this->apiKey  = env('API_KEY');
    }

    public function authentication($loginAdmin = null, $withoutFormParams = false)
    {
        // Not form params for testing fail
        $formParams = !$withoutFormParams ? ['key' => $this->apiKey] : [];

        $request = $this->http->request('POST', 'authenticate'. ($loginAdmin ? '/'. $loginAdmin : ''), [
            'form_params'   => $formParams
        ]);

        try {
            $this->accessToken = json_decode($request->getBody())->access_token;
        } catch (Exception $e) {
            throw new ApiClientException('Cannot get access token');
        }

        return $request;
    }

    public function authenticateToken($token = null)
    {
        $token = $token ?? $this->accessToken;

        return $this->http->request('POST', 'authenticate/check-access-token', [
            'form_params'   => [
                'key'           => $this->apiKey,
                'access_token'  => $token
            ]
        ]);
    }

    public function getToken()
    {
        return $this->accessToken;
    }

    public function setToken($token)
    {
        return $this->accessToken = $token;
    }

    public function setApiKey($key)
    {
        return $this->apiKey;
    }
}