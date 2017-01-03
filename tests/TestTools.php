<?php

use Faker\Factory as Faker;
use App\{
    Client,
    Goods,
    Miner
};
use App\Ghost\Repositories\Goods\GoodsManager;
use App\Ghost\Repositories\Goods\GoodsOrder;

class TestTools
{
    /**
     * @var TestSessionStorage
     */
    public $storage;

    /**
     * @var GoodsManager
     */
    public $goodsManager;

    /**
     * @var GoodsOrder
     */
    public $goodsOrder;

    public function __construct()
    {
        $this->storage = new DataStore('tests_temporary_data');

        $this->goodsManager = new GoodsManager();
        $this->goodsOrder   = new GoodsOrder();
    }

    public function clientWithOrder()
    {
        $miner  = Miner::first();
        $client = Client::whereNotNull('comment')->first();

        $goods  = Goods::first();

        $goodsFromPrice = $this->goodsManager->addGoodsPrice([
            'goods_id'  => $goods->id,
            'miner_id'  => $miner->id,
            'weight'    => 0.55,
            'address'   => 'Here the address',
            'cost'      => 1000
        ]);

        $order = $this->goodsOrder->create([
            'goods_id'  => $goods->id,
            'client_id' => $client->id,
            'weight'    => $goodsFromPrice->weight,
            'comment'   => $client->comment,
            'cost'      => $goodsFromPrice->cost
        ]);

        $this->storage->add('goods_price', $goodsFromPrice->id);
        $this->storage->add('goods_orders', $order->id);

        return (object)(compact('miner', 'client', 'goods', 'order') + ['goods_price' => $goodsFromPrice]);
    }

    public function createGoodsToPrice($count = 1)
    {
        $goods = Goods::first();
        $miner = Miner::first();
        $faker = Faker::create();

        $i = 0;
        $goodsPrice = [];
        while ($i < $count) {

            $goodsToPrice = $this->goodsManager->addGoodsPrice([
                'goods_id'  => $goods->id,
                'miner_id'  => $miner->id,
                'address'   => $faker->streetAddress,
                'weight'    => 1,
                'cost'      => 2500
            ]);

            $goodsPrice[] = $goodsToPrice;

            $this->storage->add('goods_price', $goodsToPrice->id);

            $i++;
        }

        return collect($goodsPrice);
    }

    public function cleaningTemporaryRows()
    {
        $tablesAndPkId = $this->storage->getAllData();

        foreach ($tablesAndPkId as $table => $ids) {
            foreach ($ids as $id) {
                DB::table($table)->where('id', $id)->delete();
            }
        }

        $this->storage->flush();
    }
}