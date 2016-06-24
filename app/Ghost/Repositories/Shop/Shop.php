<?php

namespace App\Ghost\Repositories\Shop;

use App\GoodsPrice;
use App\Goods;
use App\City;

abstract class Shop
{
    /**
     * @var GoodsPrice
     */
    protected $goodsPrice;

    /**
     * @var Goods
     */
    protected $goods;

    /**
     * @var City
     */
    protected $city;

    public function __construct()
    {
        $this->goodsPrice   = new GoodsPrice();
        $this->goods        = new Goods();
        $this->city         = new City();
    }
}