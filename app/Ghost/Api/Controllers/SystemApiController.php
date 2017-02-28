<?php

namespace App\Ghost\Api\Controllers;

use Illuminate\Foundation\Bus\DispatchesJobs;
use App\{
    QiwiTransaction,
    QiwiTransactionAbuse,
    Client,
    Purse
};
use App\Ghost\Repositories\Goods\Exceptions\{
    GoodsEndedException,
    NotEnoughMoney
};
use App\Jobs\{
    MadePurchase,
    QiwiTransaction as JobQiwiTransaction
};
use Validator,
    Queue;

class SystemApiController extends BaseApiController
{
    use DispatchesJobs;

    public function getProcessingGoodsOrders()
    {
        // Money transfer to client
        $newTransaction = QiwiTransaction::whereStatus(0)->orderBy('id', 'ASC')->get();

        $clientsIdsUpdatedBalance  = [];
        $numberNewTransactions     = $newTransaction->count();
        $numberSuccessfulTrans     = 0;
        $transactionsIdsAbuse      = [];

        // Processing for news a transactions
        foreach ($newTransaction as $transaction) {

            // Checking for existence user with this comment
            if (!$transaction->comment || !$client = Client::whereComment($transaction->comment)->first()) {
                $transactionsIdsAbuse[] = $transaction->id;

                $transaction->update(['status' => 1]);
                continue;
            }

            // Correction a amount
            $amount = $transaction->amount;

            if ($amount > 300) {
                $amount -= $amount % 100;
            }

            $client->increment('balance', $amount);

            $numberSuccessfulTrans++;

            $clientsIdsUpdatedBalance[] = $client->id;

            $transaction->update(['status' => 1]);
        }

        // Get all a users with positive a balance
        $usersWithBalances = Client::where('balance', '>', 0)->get();

        // Processing a orders
        $ordersIdsSuccessful       = [];
        $ordersIdsEndedGoods       = [];
        $ordersIdsNotEnoughMoney   = [];
        $wasPurchasesIds           = [];

        if (count($usersWithBalances)) {
            foreach ($usersWithBalances as $clientWithBalance) {

                $client = Client::with(['goodsOrders' => function ($query) {
                    $query->where('status', '!=', 1);
                }])->find($clientWithBalance->id);

                foreach ($client->goodsOrders as $order) {
                    try {
                        $purchase = $this->goodsOrder->buy($order);

                        $wasPurchasesIds[]     = $purchase->id;
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

        // Insert abuse transactions
        if (count($transactionsIdsAbuse)) {
            foreach ($transactionsIdsAbuse as $transId) {
                QiwiTransactionAbuse::create(['transaction_id' => $transId]);
            }
        }

        $infoProcessing = [
            'client_ids_updated_balance'    => $clientsIdsUpdatedBalance,
            'orders_ids_successful'         => $ordersIdsSuccessful,
            'orders_ids_ended_goods'        => $ordersIdsEndedGoods,
            'orders_ids_not_enough_money'   => $ordersIdsNotEnoughMoney,
            'number_successfull_trans'      => $numberSuccessfulTrans,
            'transactions_ids_abuse'        => $transactionsIdsAbuse,
            'purchases_ids'                 => $wasPurchasesIds
        ];

        // Add a job about purchase
        if (count($infoProcessing['orders_ids_successful'])) {
            $this->dispatch(
                (new MadePurchase($infoProcessing))->onQueue('made_purchase')
            );
        }
        // Add a job about abuse transactions
        if (count($infoProcessing['transactions_ids_abuse'])) {
            $this->dispatch(
                (new JobQiwiTransaction($infoProcessing))
            );
        }

        return response()->json($this->apiResponse->ok(['data' => $infoProcessing]));
    }

    public function postPurseUpdateBalance()
    {
        $valid = Validator::make(app('request')->all(), [
            'phone'     => 'required|integer',
            'balance'   => 'required'
        ]);

        if ($valid->fails()) {
            return response()->json($this->apiResponse->error($valid->messages()->getMessages()), 400);
        }

        $purse = Purse::wherePhone(app('request')->input('phone'))->first()->update([
            'balance'   => app('request')->input('balance')
        ]);

        return response()->json($this->apiResponse->ok());
    }
}