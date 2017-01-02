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

    protected $apiMethodMaps = [];

    const DEBUG = true;
    const DEBUG_DISPLAY_DUMP_FULL_EXCEPTION = false;

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

    public function removeTokenStore()
    {
        return app('filesystem')->delete($this->fileStoreToken);
    }

    public function handleResponseApi(\GuzzleHttp\Psr7\Response $response)
    {
        if ($response->getReasonPhrase() != 'OK' && $response->getReasonPhrase() != 'Created') {
            $exception = new BaseApiException('Bad request, api return status code '. $response->getStatusCode());

            $exception->setResponseContent($response->getBody());
            $exception->setStatusCode($response->getStatusCode());

            throw $exception;
        }

        if (!in_array('application/json', $response->getHeader('Content-Type'))) {
            throw new BaseApiException('Wrong response content');
        }

        return json_decode($response->getBody());
    }

    public function run($apiMethod, $params, Closure $closure)
    {
        if (!in_array($apiMethod, $this->apiMethodMaps)) throw new BaseApiException("Method {$apiMethod} not found");

        $insideMethod = camel_case(str_replace('.', '-', $apiMethod));

        if (method_exists($this, $insideMethod)) {
            try {
                return $closure($this->$insideMethod($params), null);
            } catch (UsersApiException $e) {
                return $closure($e->hasApiResponse() ? $e->getResponseJson() : null, $e);
            }
        } else {
            throw new BaseApiException("Method {$insideMethod} not found");
        }

        return false;
    }

    public static function throwException($exception)
    {
        if (!is_null($exception)) {

            if (BaseApi::DEBUG) {

                if (BaseApi::DEBUG_DISPLAY_DUMP_FULL_EXCEPTION) {
                    dump($exception);
                }

                if ($exception instanceof BaseApiException) {
                    dump('Body response api:', $exception->getResponseJson());
                }
            }

            throw $exception;
        }
    }
}