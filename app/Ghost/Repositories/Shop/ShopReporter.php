<?php

namespace App\Ghost\Repositories\Shop;

class ShopReporter extends Shop
{
    public function getPriceList()
    {
        $cities = $this->city->with('goods')->get();

        $priceList = [];

        foreach ($cities as $city) {
            $priceList[$city->name] = [
                'city_id'   => $city->id,
                'goods'     => []
            ];

            foreach ($city->goods as $goods) {
                $priceList[$city->name]['price'][$goods->name] = [
                    'goods_id'  => $goods->id,
                    'weights'   => []
                ];

                $goodsPrice = $goods->goodsPrice()->groupBy('weight')->orderBy('weight', 'ASC')->get();

                foreach ($goodsPrice as $goodsPriceItem) {
                    $priceList[$city->name]['price'][$goods->name]['weights'][] = [
                        'weight'    => round($goodsPriceItem->weight, 1),
                        'cost'      => $goodsPriceItem->cost
                    ];
                }
            }
        }

        return $priceList;
    }
}