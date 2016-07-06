<?php

namespace App\Ghost\Api\Controllers;

use App\QiwiTransaction;
use App\GoodsPrice;
use App\GoodsPurchase;
use App\City;
use App\Purse;
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
            $goods[$item->goods->city->name][$item->goods->name][wcorrect($item->weight)][] = array_merge(array_only($item->getAttributes(), ['id', 'goods_id', 'miner_id', 'reserve', 'cost', 'created_at', 'updated_at']), [
                'city_name'     => $item->goods->city->name,
                'goods_name'    => $item->goods->name,
                'address'       => $item->address,
                'weight'        => wcorrect($item->weight)
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

            $purchases[] = GoodsPurchase::create(array_only($item->getAttributes(), ['goods_id', 'miner_id', 'cost']) + [
                    'city_id'   => $item->goods->city->id,
                    'address'   => $item->address,
                    'weight'    => wcorrect($item->weight)
            ])->toArray();

            $item->delete();
        }

        return response()->json($this->apiResponse->ok(['data' => $purchases]));
    }

    public function getGoodsPriceAvailable()
    {
        $goodsAvailable = [];

        $cities = City::with('goods.goodsPrice')->get();

        foreach ($cities as $city) {
            if ($city->goods->count()) {
                $goodsAvailable[$city->name] = [];

                $cityGoods = $city->goods;

                foreach ($cityGoods as $goodsType) {
                    $goodsTypeWeights = $goodsType->goodsPrice()->groupBy('weight')->get();

                    if ($goodsTypeWeights->count()) {
                        $goodsAvailable[$city->name][$goodsType->name] = [];

                        foreach ($goodsTypeWeights as $goodsWeight) {
                            $weight = $goodsWeight->weight;
                            $weightCount = $goodsType->goodsPrice()->whereWeight($weight)->count();

                            $goodsAvailable[$city->name][$goodsType->name][wcorrect($weight)] = $weightCount;
                        }
                    }
                }
            }
        }

        return response()->json($this->apiResponse->ok(['data' => $goodsAvailable]));
    }

    public function getPurse()
    {
        $purses = Purse::orderBy('selected', 'DESC')->get();

        return response()->json($this->apiResponse->ok(['data' => $purses]));
    }

    public function postPurseSet()
    {
        $valid = Validator::make($this->request->all(), [
            'id'    => 'required|integer'
        ]);

        if ($valid->fails()) {
            return response()->json($this->apiResponse->error($valid->messages()->getMessages()), 400);
        }

        Purse::whereSelected(1)->update(['selected' => 0]);

        $purse = Purse::find($this->request->input('id'));

        $purse->update(['selected' => 1]);

        file_put_contents(storage_path('node/purse.txt'), $purse->phone .'|'. $purse->pass);

        return response()->json($this->apiResponse->ok());
    }
}