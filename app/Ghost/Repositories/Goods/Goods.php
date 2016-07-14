<?php

namespace App\Ghost\Repositories\Goods;

use App\Goods as GoodsModel;
use App\GoodsPrice;
use App\City;
use App\GoodsReserve;
use App\GoodsOrder;
use App\Client;
use App\Ghost\Libs\GibberishAES;
use App\Ghost\Repositories\Traits\BaseRepoTrait;

abstract class Goods
{

    use BaseRepoTrait;

    /**
     * @var GoodsModel
     */
    public $goods;

    /**
     * @var GoodsPrice
     */
    public $goodsPrice;

    /**
     * @var City
     */
    public $city;

    /**
     * @var GoodsReserve
     */
    public $goodsReserve;

    /**
     * @var GoodsOrder
     */
    public $goodsOrder;

    /**
     * @var Client
     */
    public $client;

    public function __construct()
    {
        $this->goods        = new GoodsModel();
        $this->city         = new City();
        $this->goodsPrice   = new GoodsPrice();
        $this->goodsReserve = new GoodsReserve();
        $this->goodsOrder   = new GoodsOrder();
        $this->client       = new Client();
    }

    public function findCity($city)
    {
        if (is_numeric($city)) {
            $city = $this->city->findOrFail($city);
        } else {
            $city = $this->city->where('name', $city)->firstOrFail();
        }

        return $city;
    }

    public function findGoods($goods)
    {
        return $this->goods->findOrFail($goods);
    }

    public function getCityId($city)
    {
        if (is_numeric($city)) {
            $city = $this->city->findOrFail($city);
        } else {
            $city = $this->city->where('name', $city)->firstOrFail();
        }

        return $city->id;
    }

    public function getGoodsId($goods)
    {
        if (is_string($goods)) {
            $goodsModel = $this->goods->where('name', $goods)->firstOrFail();
        } else {
            $goodsModel = $this->goods->findOrFail($goods);
        }

        return $goodsModel->id;
    }

    public function findGoodsPrice($id)
    {
        return $this->goodsPrice->findOrFail($id);
    }

    public function findOrder($id)
    {
        return $this->goodsOrder->findOrFail($id);
    }
}