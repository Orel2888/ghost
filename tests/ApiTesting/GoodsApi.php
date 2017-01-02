<?php

class GoodsApi extends BaseApi
{
    protected $apiMethodMaps = [
        'goods.pricelist'
    ];

    public function goodsPricelist()
    {
        try {
            $response = $this->http->request('GET', 'goods.pricelist', [
                'query' => ['access_token' => $this->getToken(true)]
            ]);
        } catch (\GuzzleHttp\Exception\ClientException $e) {
            $response = $e->getResponse();
        }

        return $this->handleResponseApi($response);
    }
}