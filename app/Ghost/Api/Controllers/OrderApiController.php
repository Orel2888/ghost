<?php

namespace App\Ghost\Api\Controllers;

use Validator;

class OrderApiController extends BaseApiController
{
    public function postCreate()
    {
        $valid = Validator::make($this->request, [
            'goods_id'  => 'required|integer',
            'weight'    => 'required',
            'count'     => 'required|integer',
            'client_id' => 'required|integer'
        ]);

        if ($valid->fails()) {
            return response()->json($this->apiResponse->error($valid->messages()->getMessages()), 400);
        }

        if ($this->goodsOrder->existsGoods($this->request->input('goods_id'), $this->request->input('weight'))) {

        } else {
            return response()->json($this->request->input(''));
        }
    }
}