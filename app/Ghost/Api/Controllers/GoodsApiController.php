<?php

namespace App\Ghost\Api\Controllers;

class GoodsApiController extends BaseApiController
{
    public function getPriceList()
    {
        $priceList = $this->goodsManager->getPriceList();

        return response()->json($this->apiResponse->ok(['data' => $priceList]));
    }
}