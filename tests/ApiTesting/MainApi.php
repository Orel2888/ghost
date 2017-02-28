<?php

class MainApi extends BaseApi
{
    public function makeBadRequest(\Closure $closure)
    {
        try {
            $response = $this->http->request('GET', 'users.find', [
                'query' => ['test_exception' => 1]
            ]);
        } catch (\GuzzleHttp\Exception\ClientException $e) {
            $response = $e->getResponse();
        }

        $responseToJson = json_decode($response->getBody());

        if (is_null($responseToJson)) throw new MainApiException('Not json data by bad request');

        return $closure($response, $responseToJson);
    }

}