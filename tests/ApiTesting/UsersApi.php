<?php

class UsersApi extends BaseApi
{
    protected $apiMethodMaps = [
        'users.find'
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

    public function usersReg($params)
    {

    }
}