<?php

use Faker\Factory as Faker;
use App\{
    Client,
    Goods,
    Miner,
    QiwiTransaction
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

    public function clientWithOrder($countOrder = 1)
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

        $orderModels = collect();

        $i = 0;
        while ($i < $countOrder) {
            $order = $this->goodsOrder->create([
                'goods_id' => $goods->id,
                'client_id' => $client->id,
                'weight' => $goodsFromPrice->weight,
                'comment' => $client->comment,
                'cost' => $goodsFromPrice->cost
            ]);

            $orderModels->push($order);

            $this->storage->add('goods_orders', $order->id);

            $i++;
        }

        $this->storage->add('goods_price', $goodsFromPrice->id);

        return (object)(compact('miner', 'client', 'goods') + [
            'goods_price' => $goodsFromPrice,
            'order'       => $orderModels->count() > 1 ? $orderModels : $orderModels[0]
        ]);
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

    public function createTransaction($amount = 0, $comment = '', $count = 1)
    {
        $transaction = factory(QiwiTransaction::class, $count)->create(compact('amount', 'comment'));

        if ($count > 1) {
            $transaction->each(function ($item) {
                $this->storage->add('qiwi_transactions', $item->id);
            });
        } else {
            $this->storage->add('qiwi_transactions', $transaction->id);
        }

        return $transaction;
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