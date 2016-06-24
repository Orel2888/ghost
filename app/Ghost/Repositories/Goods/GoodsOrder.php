<?php

namespace App\Ghost\Repositories\Goods;

use App\Ghost\Repositories\Traits\BaseRepoTrait;
use App\Ghost\Repositories\Goods\Exceptions\GoodsEndedException;
use App\Ghost\Repositories\Goods\Exceptions\NotEnoughMoney;
use App\GoodsPurchase;

class GoodsOrder extends Goods
{
    use BaseRepoTrait;

    public function create(array $attributes)
    {
        $attributesRequired = [
            'goods_id',
            'client_id',
            'weight',
            'comment'
        ];

        $this->checkRequiredAttributesArray($attributes, $attributesRequired);

        return $this->goodsOrder->create(array_only($attributes, $attributesRequired));
    }

    /**
     * Check is exists a order
     * @param $clientId
     * @param $goodsId
     * @param $weight
     * @return bool
     */
    public function existsOrder($clientId, $goodsId, $weight)
    {
        return !is_null($this->goodsOrder->whereGoodsId($goodsId)->whereClientId($clientId)->whereWeight($weight)->first());
    }

    /**
     * Check is exists a goods to goods price
     * @param $goodsId
     * @param $weight
     * @return mixed
     */
    public function existsGoods($goodsId, $weight)
    {
        return !is_null($this->goodsPrice->whereGoodsId($goodsId)->whereWeight($weight)->whereReserve(0)->first());
    }

    /**
     * Get actual goods by order
     * @param $orderId
     * @return mixed
     */
    public function getGoodsPrice($orderId)
    {
        $order = $this->findOrder($orderId);

        return $this->goodsPrice->whereGoodsId($order->goods_id)->whereWeight($order->weight)->whereReserve(0)->firstOrFail();
    }

    /**
     * Check a goods is exists and solvency
     * @param $orderId
     * @param $clientId
     * @return mixed
     * @throws GoodsEndedException
     * @throws NotEnoughMoney
     */
    public function checkSolvency($orderId, $clientId)
    {
        $client = $this->client->findOrFail($clientId);
        $order  = $this->findOrder($orderId);

        if ($this->existsGoods($order->goods_id, $order->weight)) {

            $goodsPrice = $this->goodsPrice->whereGoodsId($order->goods_id)->whereWeight($order->weight)->whereReserve(0)->first();

            if ($client->balance < $goodsPrice->cost) {
                throw new NotEnoughMoney;
            } else {
                return $goodsPrice;
            }
        } else {
            throw new GoodsEndedException;
        }
    }

    public function buy($orderId, $clientId)
    {
        $goodsPrice = $this->checkSolvency($orderId, $clientId);

        $order = $this->findOrder($orderId);

        $goodsPurchase = GoodsPurchase::create([
            'city_id'   => $goodsPrice->goods->city_id,
            'goods_id'  => $goodsPrice->goods_id,
            'miner_id'  => $goodsPrice->miner_id,
            'client_id' => $order->client_id,
            'weight'    => $goodsPrice->weight,
            'address'   => $goodsPrice->address,
            'cost'      => $goodsPrice->cost
        ]);

        $goodsPrice->delete();

        $client = $order->client;
        $client->decrement('balance', $goodsPrice->cost);
        $client->increment('rating', $this->getRating($goodsPrice->cost));
        $client->increment('count_purchases', 1);

        return $goodsPurchase;
    }

    public function getRating($amount)
    {
        $rating = 0;

        if ($amount <= 1000) {
            $rating = 0.01;
        } elseif ($amount <= 2000) {
            $rating = 0.02;
        } elseif ($amount <= 3000) {
            $rating = 0.03;
        } elseif ($amount > 3000) {
            $rating = 0.04;
        }

        return $rating;
    }
}