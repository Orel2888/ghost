<?php

use GuzzleHttp\Client;

abstract class BaseApi
{
    /**
     * @var Client
     */
    protected $http;
    
    protected $apiKey;

    protected $accessToken;

    protected $dataCapture = false;

    protected $saveTokenInStorage = false;

    protected $fileStoreToken = 'tokenapi_for_tests';

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
            $accessToken = $this->accessToken = json_decode($request->getBody())->access_token;

            // Save a token
            if ($this->saveTokenInStorage) {
                $this->rememberToken($accessToken);
            }
        } catch (Exception $e) {
            throw new BaseApiException('Cannot get access token');
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

    public function getToken($fromStorage = false)
    {
        return $this->accessToken ?: $fromStorage ? $this->restoreToken() : null;
    }

    public function setToken($token)
    {
        return $this->accessToken = $token;
    }

    public function setApiKey($key)
    {
        return $this->apiKey = $key;
    }

    public function saveToken($flag = true)
    {
        $this->saveTokenInStorage = $flag;

        return $this;
    }

    public function rememberToken($token)
    {
        return app('filesystem')->put($this->fileStoreToken, $token);
    }

    public function restoreToken()
    {
        $filesystem = app('filesystem');

        return $filesystem->has($this->fileStoreToken) ? $filesystem->get($this->fileStoreToken) : null;
    }
}