<?php

namespace App\Ghost\Repositories\Goods;

class GoodsReserve extends Goods
{

    /**
     * @param array $attributes
     * @return static
     */
    public function makeReservation(array $attributes)
    {
        $attributesRequired = [
            'goods_price_id',
            'client_id',
            'time'
        ];

        $this->checkRequiredAttributesArray($attributes, $attributesRequired);

        $goodsPrice = $this->findGoodsPrice($attributes['goods_price_id']);

        $goodsReserved = $this->goodsReserve->create(array_only($attributes, $attributesRequired));

        $goodsPrice->update(['reserve' => 1]);

        return $goodsReserved;
    }


    public function checkExpired($id)
    {
        return $this->goodsReserve->whereId($id)->whereRaw('time>NOW()')->count();
    }


    public function cancel($id)
    {
        $goodsReserve = $this->goodsReserve->findOrFail($id);

        $this->findGoodsPrice($goodsReserve->goods_price_id)->update(['reserve' => 0]);

        $goodsReserve->delete();
    }

    public function perform($id)
    {
        $this->goodsReserve->findOrFail($id)->update(['status' => 1]);
    }

}