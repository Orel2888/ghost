<?php

namespace App\Ghost\Api\Controllers;

use App\QiwiTransaction;
use App\Client;
use App\Ghost\Repositories\Goods\Exceptions\GoodsEndedException;
use App\Ghost\Repositories\Goods\Exceptions\NotEnoughMoney;
use Validator;
use App\Purse;

class SystemApiController extends BaseApiController
{
    public function getProcessingGoodsOrders()
    {
        // Money transfer to client
        $newTransaction = QiwiTransaction::whereStatus(0)->orderBy('id', 'ASC')->get();

        $clientsIdsUpdatedBalance  = [];
        $numberNewTransactions     = $newTransaction->count();
        $numberSuccessfulTrans     = 0;
        $transactions_ids_abuse    = [];

        foreach ($newTransaction as $transaction) {
            if ($transaction->comment) {
                $amount = $transaction->amount;

                if ($amount > 300) {
                    $amount -= $amount % 100;
                }

                if ($client = Client::whereComment($transaction->comment)->first()) {
                    $clientsIdsUpdatedBalance[] = $client->id;

                    $client->increment('balance', $amount);

                    $numberSuccessfulTrans++;
                } else {
                    $transactions_ids_abuse[] = $transaction->id;
                }

            } else {
                $transactions_ids_abuse[] = $transaction->id;
            }

            $transaction->update(['status' => 1]);
        }

        // Processing a orders
        $ordersIdsSuccessful       = [];
        $ordersIdsEndedGoods       = [];
        $ordersIdsNotEnoughMoney   = [];

        if (count($clientsIdsUpdatedBalance)) {
            foreach ($clientsIdsUpdatedBalance as $clientId) {

                $client = Client::with(['goodsOrders' => function ($query) {
                    $query->where('status', '!=', 1);
                }])->find($clientId);

                foreach ($client->goodsOrders as $order) {
                    try {
                        $this->goodsOrder->buy($order);

                        $ordersIdsSuccessful[] = $order->id;
                    } catch (GoodsEndedException $e) {
                        $order->update(['status' => 2]);

                        $ordersIdsEndedGoods[] = $order->id;
                    } catch (NotEnoughMoney $e) {
                        $order->update(['status' => 3]);

                        $ordersIdsNotEnoughMoney[] = $order->id;
                    }
                }
            }
        }

        $infoProcessing = [
            'client_ids_updated_balance'    => $clientsIdsUpdatedBalance,
            'orders_ids_successful'         => $ordersIdsSuccessful,
            'orders_ids_ended_goods'        => $ordersIdsEndedGoods,
            'orders_ids_not_enough_money'   => $ordersIdsNotEnoughMoney,
            'number_successfull_trans'      => $numberSuccessfulTrans,
            'transactions_ids_abuse'        => $transactions_ids_abuse
        ];

        return response()->json($this->apiResponse->ok(['data' => $infoProcessing]));
    }

    public function postPurseUpdateBalance()
    {
        $valid = Validator::make($this->request->all(), [
            'phone'     => 'required|integer',
            'balance'   => 'required'
        ]);

        if ($valid->fails()) {
            return response()->json($this->apiResponse->error($valid->messages()->getMessages()), 400);
        }

        $purse = Purse::wherePhone($this->request->input('phone'))->first()->update([
            'balance'   => $this->request->input('balance')
        ]);

        return response()->json($this->apiResponse->ok());
    }
}