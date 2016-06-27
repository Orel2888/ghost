<?php

namespace App\Ghost\Api\Controllers;

use App\QiwiTransaction;
use App\GoodsPrice;
use App\GoodsPurchase;
use Validator;

class AdminApiController extends BaseApiController
{
    public function getQiwiTransaction()
    {
        $transactions = QiwiTransaction::orderBy('id', 'DESC')->limit(10)->get();
        
        return response()->json([
            'status' => 'ok', 
            'data' => array_reverse($transactions->toArray())
        ]);
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

    public function getGoodsPricePurchase()
    {
        $valid = Validator::make($this->request->all(), [
            'goods_price_id'    => 'required|numeric'
        ]);

        if ($valid->fails()) {
            return response()->json($this->apiResponse->error($valid->messages()->getMessages()), 400);
        }

        $goodsPrice = GoodsPrice::whereId($this->request->input('goods_price_id'))->first();

        if (is_null($goodsPrice)) {
            return response()->json($this->apiResponse->fail(['message' => 'goods_price_id not found']), 404);
        }

        $goodsPrice->delete();

        $purchase = GoodsPurchase::create(array_only($goodsPrice->getAttributes(), ['goods_id', 'weight', 'miner_id', 'address', 'cost']) + [
            'city_id'   => $goodsPrice->goods->city->id
        ]);

        return response()->json($this->apiResponse->ok(['data' => $purchase->toArray()]));
    }
}