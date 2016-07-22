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
     * @param $withAttributes
     * @return array [weight => integer(count)] | [weight => [attributes]]
     */
    public function getGoodsWeightsAndCount($goodsId, array $withAttributes = [])
    {
        $goodsWeights = $this->findGoods($goodsId)->goodsPrice()->groupBy('weight')->orderBy('weight', 'ASC')->get();

        $goodsWeightsCounts = [];

        foreach ($goodsWeights as $item) {

            $countWeight = $this->goodsPrice->whereGoodsId($goodsId)->whereWeight($item->weight)->count();

            if (empty($withAttributes)) {
                $weightAttributes = $countWeight;
            } else {
                $weightAttributes = ['count' => $countWeight] + array_only($item->getAttributes(), $withAttributes);
            }

            $goodsWeightsCounts[wcorrect($item->weight)] = $weightAttributes;
        }

        return $goodsWeightsCounts;
    }

    public function getPriceList()
    {
        $goodsPrice = [];

        $cities = $this->city->with('goods')->get();

        foreach ($cities as $city) {
            $cityName = $city->name;

            $goodsPrice[$cityName] = [];

            foreach ($city->goods as $goods) {
                $goodsPrice[$cityName][$goods->name] = [];

                foreach ($this->getGoodsWeightsAndCount($goods->id, ['cost']) as $weight => $data) {
                    $goodsPrice[$cityName][$goods->name][$weight] = [
                        'goods_id'  => $goods->id,
                        'count'     => $data['count'],
                        'cost'      => $data['cost']
                    ];
                }
            }
        }

        return $goodsPrice;
    }

    public function goodsPriceCheckExists($goodsId, $weight, $count)
    {
        return $this->goodsPrice->whereGoodsId($goodsId)->whereWeight($weight)->whereReserve(0)->count() >= $count;
    }

    public function getGoodsPriceByWeight($goodsId, $weight, $count)
    {
        $getGoodsPrice = $this->goodsPrice->whereGoodsId($goodsId)->whereWeight($weight)->whereReserve(0)->take($count);

        return $count == 1 ? $getGoodsPrice->first() : $getGoodsPrice->get();
    }

    public function parseAddresses($text)
    {
        preg_match_all('/\w\)\s?(.*)/', $text, $matches);

        return $matches[1];
    }
}