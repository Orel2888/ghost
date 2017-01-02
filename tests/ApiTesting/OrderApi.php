<?php

class OrderApi extends BaseApi
{
    protected $apiMethodMaps = [
        'order.create',
        'order.find',
        'order.list',
        'order.del',
        'order.delall'
    ];

    public function orderCreate(array $params)
    {
        try {
            $response = $this->http->request('POST', 'order.create', [
                'form_params'   => array_merge($params, ['access_token' => $this->getToken(true)])
            ]);
        } catch (\GuzzleHttp\Exception\ClientException $e) {
            $response = $e->getResponse();
        }

        return $this->handleResponseApi($response);
    }
}