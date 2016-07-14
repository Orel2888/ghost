<?php

namespace App\Ghost\Repositories\Goods;

use App\City;
use App\Ghost\Repositories\Miner\MinerManager;
use App\Ghost\Repositories\Goods\GoodsReserve as RepoGoodsReserve;

class GoodsManager extends Goods
{
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
        $attributesRequired = [
            'goods_name',
            'city_id'
        ];

        $this->checkRequiredAttributesArray($attributes, $attributesRequired);

        return $this->goods->create([
            'name'      => $attributes['goods_name'],
            'city_id'   => $attributes['city_id']
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

    /**
     * Weights and counts
     * @param $goodsId
     * @return array [weight => integer(count)]
     */
    public function getGoodsWeightsAndCount($goodsId)
    {
        $goodsWeights = $this->findGoods($goodsId)->goodsPrice()->groupBy('weight')->orderBy('weight', 'ASC')->get();

        $goodsWeightsCounts = [];

        foreach ($goodsWeights as $item) {
            $goodsWeightsCounts[wcorrect($item->weight)] = $this->goodsPrice->whereGoodsId($goodsId)->whereWeight($item->weight)->count();
        }

        return $goodsWeightsCounts;
    }

    public function parseAddresses($text)
    {
        preg_match_all('/\w\)\s?(.*)/', $text, $matches);

        return $matches[1];
    }
}