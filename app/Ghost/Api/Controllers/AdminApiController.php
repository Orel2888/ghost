<?php

namespace App\Ghost\Api\Controllers;

use App\QiwiTransaction;
use App\GoodsPrice;
use App\GoodsPurchase;
use Validator;
use Illuminate\Database\Eloquent\ModelNotFoundException;

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
            $goods[$item->goods->city->name][$item->goods->name][(string)round($item->weight, 1)][] = array_merge(array_only($item->getAttributes(), ['id', 'goods_id', 'miner_id', 'weight', 'address', 'reserve', 'cost', 'created_at', 'updated_at']), [
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
            'goods_price_id'    => 'required'
        ]);

        if ($valid->fails()) {
            return response()->json($this->apiResponse->error($valid->messages()->getMessages()), 400);
        }

        $goodsPriceIds = explode(',', $this->request->input('goods_price_id'));

        try {
            $goodsPrice = GoodsPrice::findOrFail($goodsPriceIds);
        } catch (ModelNotFoundException $e) {
            return response()->json($this->apiResponse->fail(['message' => 'Не удалось выбрать заданные товары']), 400);
        }

        $purchases = [];

        foreach ($goodsPrice as $item) {

            $purchases[] = GoodsPurchase::create(array_only($item->getAttributes(), ['goods_id', 'weight', 'miner_id', 'address', 'cost']) + [
                    'city_id'   => $item->goods->city->id
            ])->toArray();

            $item->delete();
        }

        return response()->json($this->apiResponse->ok(['data' => $purchases]));
    }
}