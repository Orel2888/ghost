<?php

class UsersApi extends BaseApi
{
    protected $apiMethodMaps = [
        'users.find',
        'users.reg',
        'users.update',
        'users.purse'
    ];

    /**
     * @throws UsersApiException
     * @param $tgchatId
     * @return mixed
     */
    public function usersFind($tgchatId)
    {
        try {
            $response = $this->http->request('GET', 'users.find', [
                'query' => [
                    'tg_chatid'     => $tgchatId,
                    'access_token'  => $this->getToken(true)
                ]
            ]);
        } catch (\GuzzleHttp\Exception\ClientException $e) {
            $response = $e->getResponse();
        }

        return $this->handleResponseApi($response);
    }

    /**
     * @throws UsersApiException
     * @param $params
     * @return mixed
     */
    public function usersReg($params)
    {
        try {
            $response = $this->http->request('POST', 'users.reg', [
                'form_params'   => array_merge($params, ['access_token'  => $this->getToken(true)])
            ]);
        } catch (\GuzzleHttp\Exception\ClientException $e) {
            $response = $e->getResponse();
        }

        return $this->handleResponseApi($response);
    }

    public function usersUpdate($params)
    {
        try {
            $response = $this->http->request('POST', 'users.update', [
                'form_params'   => array_merge($params, ['access_token'  => $this->getToken(true)])
            ]);
        } catch (\GuzzleHttp\Exception\ClientException $e) {
            $response = $e->getResponse();
        }

        return $this->handleResponseApi($response);
    }

    public function usersPurse()
    {
        try {
            $response = $this->http->request('GET', 'users.purse', [
                'query'   => ['access_token'  => $this->getToken(true)]
            ]);
        } catch (\GuzzleHttp\Exception\ClientException $e) {
            $response = $e->getResponse();
        }

        return $this->handleResponseApi($response);
    }
}