<?php

namespace App\Ghost\Repositories\Goods;

use App\City;
use App\Ghost\Repositories\Miner\MinerManager;
use App\Ghost\Repositories\Goods\GoodsReserve as RepoGoodsReserve;

class GoodsManager extends Goods
{
    private $cityAddGoodsName;
    private $cityAddGoodsId;

    /**
     * @var GoodsReserve
     */
    protected $repoGoodsReserve;

    /**
     * @var MinerManager
     */
    protected $repoMinerManager;

    public function __construct()
    {
        parent::__construct();

        $this->repoGoodsReserve = new RepoGoodsReserve();
        $this->repoMinerManager = new MinerManager();
    }

    /**
     * Adding product
     * @param array $attributes
     * @return \App\Goods
     */
    public function addGoods(array $attributes)
    {
        if (!isset($attributes['goods_name']) && !isset($attributes['city_id'])
            || !isset($attributes['goods_name']) && !isset($attributes['city_name'])
            || isset($attributes['city_name']) && isset($attributes['city_id'])) {
            throw new InvalidArgumentException('Required goods_name and city_id or goods_name and city_name');
        }

        if ($this->cityAddGoodsName && isset($attributes['city_name']) && $this->cityAddGoodsName != $attributes['city_name']) {
            $this->cityAddGoodsId = $this->getCityId($attributes['city_name']);
        }
        if ($this->cityAddGoodsId && isset($attributes['city_id']) && $this->cityAddGoodsId != $attributes['city_id']) {
            $this->cityAddGoodsId = $this->getCityId($attributes['city_id']);
        }
        if (!$this->cityAddGoodsName && !$this->cityAddGoodsId) {
            $this->cityAddGoodsId = isset($attributes['city_id']) ? $this->getCityId($attributes['city_id']) : $this->getCityId($attributes['city_name']);
        }

        return $this->goods->create([
            'name'      => $attributes['goods_name'],
            'city_id'   => $this->cityAddGoodsId
        ]);
    }

    public function addGoodsPrice(array $goods, array $reserveAttributes = [])
    {
        $attributesRequired = [
            'goods_id',
            'miner_id',
            'weight',
            'address',
            'cost'
        ];

        $this->checkRequiredAttributesArray($goods, $attributesRequired);

        $goodsAttributes = array_only($goods, $attributesRequired);

        $goodsPrice = $this->goodsPrice->create($goodsAttributes);

        if (!empty($reserveAttributes)) {
            $this->repoGoodsReserve->makeReservation(array_merge($reserveAttributes, ['goods_price_id' => $goodsPrice->id]));
        }

        return $goodsPrice;
    }

    public function addGoodsPriceFromText()
    {

    }

    public function parseAddreses($text)
    {
        preg_match_all('/\w\)\s?(.*)/', $text, $matches);

        return $matches[1];
    }
}