<?php

namespace App\Ghost\Api\Controllers;

use App\QiwiTransaction;
use App\GoodsPrice;

class AdminApiController extends BaseApiController
{
    public function getQiwiTransaction()
    {
        $transactions = QiwiTransaction::orderBy('id', 'DESC')->limit(10)->get()->reverse();

        return response()->json($this->apiResponse->ok(['data' => $transactions->toArray()]));
    }

    public function getGoodsPrice()
    {
        $goodsPrice = GoodsPrice::with('goods.city')->get();

        $goods = [];

        foreach ($goodsPrice as $item) {
            $goods[$item->goods->city->name][$item->goods->name][] = array_merge(array_only($item->getAttributes(), ['id', 'goods_id', 'miner_id', 'weight', 'address', 'reserve', 'cost', 'created_at', 'updated_at']), [
                'city_name'     => $item->goods->city->name,
                'goods_name'    => $item->goods->name,
                'address'       => $item->address
            ]);
        }

        return response()->json($this->apiResponse->ok(['data' => $goods]));
    }
}