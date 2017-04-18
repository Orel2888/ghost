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
        $ordersPending = $this->goodsOrder->countOrderToUser($client->id, 'pending');

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

        // Cleaning orders by limit
        if (count($orderIdsProcessed)) {
            $this->goodsOrder->cleaningOrderToUser($client->id, 'successful');
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
            'id'        => 'required|regex:/^\d+(,\d+)*$/',
            'client_id' => 'required|integer'
        ]);

        $input     = app('request')->all();
        $ordersIds = explode(',', $input['id']);

        if ($valid->fails()) {
            return response()->json($this->apiResponse->error($valid->messages()->getMessages()), 400);
        }

        $orders = GoodsOrder::with(['goods.city', 'purchase'])
            ->whereClientId($input['client_id'])
            ->whereIn('id', $ordersIds)
            ->get();

        if ($orders->isEmpty()) {
            return response()->json($this->apiResponse->fail(['message' => 'Заказ(ы) не найден']), 404);
        }

        $orderInfoCreator = function ($order) {

            $orderData =  [
                'city_name'         => $order->goods->city->name,
                'goods_name'        => $order->goods->name,
                'weight'            => wcorrect($order->weight),
                'cost'              => $order->cost,
                'id'                => $order->id,
                'status'            => $order->status,
                'status_message'    => $this->goodsOrder->statusOrderMessages[$order->status],
                'date'              => $order->created_at->isToday() ? $order->created_at->diffForHumans() : $order->created_at->format('d.m.y H:i'),
            ];

            $orderData = array_merge($orderData, [
                'address'   => $order->status == 1 ? $order->purchase->address : null
            ]);

            return $orderData;
        };

        $ordersCollection = $orders->map(function ($order, $key) use($orderInfoCreator) {
            return $orderInfoCreator($order);
        });

        return response()->json($this->apiResponse->ok(['data' => $ordersCollection->toArray()]));
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
            $orderStatus = $this->goodsOrder->statusCategories[app('request')->input('status')];
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
            'order_id'  => 'required|regex:/^\d+(,\d+)*$/'
        ]);

        if ($valid->fails()) {
            return response()->json($this->apiResponse->error($valid->messages()->getMessages()), 400);
        }

        $input = app('request')->all();

        $ordersIds = explode(',', $input['order_id']);

        $orderRemoved = GoodsOrder::whereClientId($input['client_id'])->whereIn('id', $ordersIds)->delete();

        if (!$orderRemoved) {
            return response()->json($this->apiResponse->fail(), 400);
        }

        return response()->json($this->apiResponse->ok(['method' => 'del']));
    }

    public function postDelAllOrder()
    {
        $valid = Validator::make(app('request')->all(), [
            'client_id' => 'required|integer',
            'status'    => 'in:pending,successful'
        ]);

        if ($valid->fails()) {
            return response()->json($this->apiResponse->error($valid->messages()->getMessages()), 400);
        }

        if (app('request')->has('status'))
            $orderStatus = $this->goodsOrder->statusCategories[app('request')->input('status')];
        else
            $orderStatus = [0, 1, 2, 3];

        GoodsOrder::whereClientId(app('request')->input('client_id'))->whereIn('status', $orderStatus)->delete();

        return response()->json($this->apiResponse->ok(['method' => 'delall']));
    }
}