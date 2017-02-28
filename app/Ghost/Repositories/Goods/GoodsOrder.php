<?php

namespace App\Ghost\Repositories\Goods;

use App\Ghost\Repositories\Traits\BaseRepoTrait;
use App\Ghost\Repositories\Goods\Exceptions\GoodsEndedException;
use App\Ghost\Repositories\Goods\Exceptions\NotEnoughMoney;
use App\Ghost\Repositories\Goods\Exceptions\GoodsNotFound;
use App\GoodsPurchase;
use App\Ghost\Repositories\Goods\GoodsManager;
use Illuminate\Contracts\Bus\Dispatcher;
use App\Jobs\MadePurchase;

class GoodsOrder extends Goods
{
    use BaseRepoTrait;

    public $goodsManager;

    public $statusOrderMessages = [
        0 => 'В обработке',
        1 => 'Оплачен',
        2 => 'Нет товара',
        3 => 'Недостаточно средств'
    ];

    public function __construct()
    {
        parent::__construct();

        $this->goodsManager = new GoodsManager();
    }

    public function create(array $attributes)
    {
        $attributesRequired = [
            'goods_id',
            'client_id',
            'weight',
            'comment',
            'cost'
        ];

        $this->checkRequiredAttributesArray($attributes, $attributesRequired);

        // Check to exists the such goods to price
        if (!$this->goodsManager->checkGoodsPrice($attributes['goods_id'], $attributes['weight'], $attributes['cost'])) {
            throw new GoodsNotFound('Cannot create order because such goods not found to price');
        }

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
     * @param $count
     * @return mixed
     */
    public function existsGoods($goodsId, $weight, $count = 1)
    {
        return $this->goodsManager->goodsPriceCheckExists($goodsId, $weight, $count);
    }

    /**
     * Get actual goods by order
     * @param $orderId
     * @return mixed
     */
    public function getGoodsPrice($orderId)
    {
        if ($orderId instanceof $this->goodsOrder) {
            $order = $orderId;
        } else {
            $order = $this->findOrder($orderId);
        }

        return $this->goodsManager->getGoodsPriceByWeight($order->goods_id, $order->weight, 1);
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
        if ($orderId instanceof $this->goodsOrder) {
            $client = $orderId->client;
            $order  = $orderId;
        } else {
            $client = $this->client->findOrFail($clientId);
            $order  = $this->findOrder($orderId);
        }

        if ($this->existsGoods($order->goods_id, $order->weight)) {
            if ($client->balance < $order->cost) {
                throw new NotEnoughMoney;
            } else {
                return $this->getGoodsPrice($order);
            }
        } else {
            throw new GoodsEndedException;
        }
    }

    public function buy($orderId)
    {
        if ($orderId instanceof $this->goodsOrder) {
            $order = $orderId;
        } else {
            $order = $this->findOrder($orderId);
        }

        $goodsPrice = $this->checkSolvency($order->id, $order->client_id);

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

        $order->update(['purchase_id' => $goodsPurchase->id, 'status' => 1]);

        $client = $order->client;
        $client->decrement('balance', $goodsPrice->cost);
        $client->increment('rating', $this->getRating($goodsPrice->cost));
        $client->increment('count_purchases', 1);

        return $goodsPurchase;
    }

    public function buyProcessingOrder($order, $jobNotify = true)
    {
        if (!$order instanceof $this->goodsOrder) {
            $order = $this->goodsOrder->find($order);
        }

        try {
            $purchase = $this->buy($order);

            if (config('shop.test_execute_async_jobs') && $jobNotify) {
                // Job for notification about was purchase
                app(Dispatcher::class)->dispatch(
                    (new MadePurchase(['orders_ids_successful' => [$order->id]]))->onQueue('made_purchase')
                );
            }

            return $purchase;
        } catch (GoodsEndedException $e) {
            $order->update(['status' => 2]);

            return $e;
        } catch (NotEnoughMoney $e) {
            $order->update(['status' => 3]);

            return $e;
        }

        return false;
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