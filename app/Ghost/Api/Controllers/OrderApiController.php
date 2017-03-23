<?php

namespace App\Ghost\Api\Controllers;

use Validator;
use App\{
    Client,
    GoodsOrder,
    Purse,
    GoodsPurchase
};

class OrderApiController extends BaseApiController
{
    public function postCreate()
    {
        $valid = Validator::make(app('request')->all(), [
            'goods_id'  => 'required|integer',
            'weight'    => 'required',
            'count'     => 'required|integer',
            'client_id' => 'required|integer'
        ]);

        if ($valid->fails()) {
            return response()->json($this->apiResponse->error($valid->messages()->getMessages()), 400);
        }

        $input = app('request')->all();

        $client = Client::find($input['client_id']);

        // Get limit order for client
        $limitOrder = config('shop.order_count_user');

        // Check to limit pending a orders for user
        $ordersPending = GoodsOrder::whereClientId($client->id)->whereIn('status', [0, 2, 3])->count();

        if (($ordersPending + $input['count']) > $limitOrder) {

            $countOrderForRemove = ($ordersPending + $input['count']) - $limitOrder;

            $message = "Вы можете хранить в корзине {$limitOrder} ". trans_choice('shop.order', $limitOrder)
                .", у вас не хватает места. Удалите {$countOrderForRemove} "
                . trans_choice('shop.order', $countOrderForRemove) ." из вашей корзины.";

            return response()->json($this->apiResponse->fail(compact('message')));
        }

        // Check to available a product
        if (!$this->goodsManager->goodsPriceCheckExists($input['goods_id'], $input['weight'], $input['count'])) {

            if ($input['count'] > 1) {
                $message = 'Не такого количества товара, попробуйте уменьшить количество';
            } else {
                $message = 'Нет товара или он кончился в самый неподходящий момент';
            }

            return response()->json($this->apiResponse->fail(compact('message')));
        }

        $oneGoodsPrice = $this->goodsOrder->goodsPrice->whereGoodsId($input['goods_id'])->whereWeight($input['weight'])->first();

        $orderIds     = [];
        $ordersModels = [];

        for ($i = 0; $i<$input['count']; $i++) {

            $order = $this->goodsOrder->create([
                'goods_id'  => $input['goods_id'],
                'weight'    => $input['weight'],
                'client_id' => $input['client_id'],
                'comment'   => $client->comment,
                'cost'      => $oneGoodsPrice->cost
            ]);

            $ordersModels[] = $order;

            $orderIds[] = $order->id;
        }

        // If client balance enough for purchase, immediately proccing orders
        $orderIdsProcessed = [];

        if ($client->balance >= $ordersModels[0]->cost) {
            foreach ($ordersModels as $order) {
                if ($this->goodsOrder->buyProcessingOrder($order) instanceof GoodsPurchase) {
                    $orderIdsProcessed[] = $order->id;
                }
            }
        }

        // Cleaning early the successful a orders
        if (count($orderIdsProcessed)) {
            $clientSuccessfulOrders = GoodsOrder::whereClientId($client->id)->whereStatus(1);

            $countSuccessfulOrders = $clientSuccessfulOrders->count();

            $countSuccessfulOrderRemove = $countSuccessfulOrders > $limitOrder
                ? $countSuccessfulOrders - $limitOrder
                : false;

            if ($countSuccessfulOrderRemove) {
                $clientSuccessfulOrders->orderBy('id', 'ASC')->limit($countSuccessfulOrderRemove)->delete();
            }
        }

        // Information about a created orders
        $order->with('goods.city');

        $orderInfo = [
            'city_name'         => $order->goods->city->name,
            'goods_name'        => $order->goods->name,
            'count'             => $input['count'],
            'weight'            => $input['weight'],
            'cost'              => $oneGoodsPrice->cost * $input['count'],
            'purse'             => Purse::whereSelected(1)->first()->phone,
            'order_processed'   => count($orderIdsProcessed)
        ];

        return response()->json($this->apiResponse->ok([
            'data' => array_merge($orderInfo, ['order_ids' => $orderIds])
        ]));
    }

    public function getFind()
    {
        $valid = Validator::make(app('request')->all(), [
            'id'        => 'required|integer',
            'client_id' => 'required|integer'
        ]);

        if ($valid->fails()) {
            return response()->json($this->apiResponse->error($valid->messages()->getMessages()), 400);
        }

        $order = GoodsOrder::with(['goods.city', 'purchase'])
            ->whereIdAndClientId(app('request')->input('id'), app('request')->input('client_id'))
            ->first();

        if (is_null($order)) {
            return response()->json($this->apiResponse->fail(['message' => 'Заказ не найден']), 404);
        }

        list($weight, $cost, $id) = [$order->weight, $order->cost, $order->id];

        $orderInfo = [
            'city_name'         => $order->goods->city->name,
            'goods_name'        => $order->goods->name,
            'weight'            => wcorrect($order->weight),
            'cost'              => $order->cost,
            'id'                => $order->id,
            'status'            => $order->status,
            'status_message'    => $this->goodsOrder->statusOrderMessages[$order->status],
            'date'              => $order->created_at->isToday() ? $order->created_at->diffForHumans() : $order->created_at->format('d.m.y H:i'),
        ];

        $orderInfo = array_merge($orderInfo, [
            'address'   => $order->status == 1 ? $order->purchase->address : null
        ]);

        return response()->json($this->apiResponse->ok(['data' => $orderInfo]));
    }

    public function getList()
    {
        $valid = Validator::make(app('request')->all(), [
            'client_id' => 'required|integer',
            'status'    => 'in:pending,successful'
        ]);

        if ($valid->fails()) {
            return response()->json($this->apiResponse->error($valid->messages()->getMessages()), 400);
        }

        $client = Client::findOrFail(app('request')->input('client_id'));

        $orderStatus = [0, 1, 2, 3];

        if (app('request')->has('status')) {
            switch (app('request')->input('status')) {
                case 'pending':
                    $orderStatus = [0, 2, 3];
                break;

                case 'successful':
                    $orderStatus = [1];
                break;
            }
        }

        $orders = GoodsOrder::whereClientId($client->id)
            ->whereIn('status', $orderStatus)
            ->orderBy('id', 'ASC')
            ->take(config('shop.order_count_user'))
            ->get();

        $ordersData = [];

        foreach ($orders as $order) {
            $ordersData[] = [
                'id'                => $order->id,
                'city_name'         => $order->goods->city->name,
                'goods_name'        => $order->goods->name,
                'cost'              => $order->cost,
                'weight'            => wcorrect($order->weight),
                'status'            => $order->status,
                'status_message'    => $this->goodsOrder->statusOrderMessages[$order->status],
                'date'              => $order->created_at->isToday() ? $order->created_at->diffForHumans() : $order->created_at->format('d.m.y H:i'),
                'address'           => $order->purchase_id ? $order->purchase->address : null
            ];
        }

        $dataResponse = [
            'data'  => $ordersData,
            'count' => $orders->count()
        ];

        return response()->json($this->apiResponse->ok($dataResponse));
    }

    public function postDelOrder()
    {
        $valid = Validator::make(app('request')->all(), [
            'client_id' => 'required|integer',
            'order_id'  => 'required|integer'
        ]);

        if ($valid->fails()) {
            return response()->json($this->apiResponse->error($valid->messages()->getMessages()), 400);
        }

        $input = app('request')->all();

        $goodsOrder = GoodsOrder::whereId($input['order_id'])->whereClientId($input['client_id'])->first();

        if (is_null($goodsOrder)) {
            return response()->json($this->apiResponse->fail(), 400);
        }

        $goodsOrder->delete();

        return response()->json($this->apiResponse->ok(['method' => 'del']));
    }

    public function postDelAllOrder()
    {
        $valid = Validator::make(app('request')->all(), [
            'client_id' => 'required|integer'
        ]);

        if ($valid->fails()) {
            return response()->json($this->apiResponse->error($valid->messages()->getMessages()), 400);
        }

        GoodsOrder::whereClientId(app('request')->input('client_id'))->delete();

        return response()->json($this->apiResponse->ok(['method' => 'delall']));
    }
}