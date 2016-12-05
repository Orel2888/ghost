<?php

namespace App\Listeners;

use App\Events\WasPurchases;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Longman\TelegramBot\Request as TgRequest;
use Longman\TelegramBot\Exception\TelegramException;
use App\GoodsOrder;

class NotifyClientToTelegram
{

    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  WasPurchases  $event
     * @return void
     */
    public function handle(WasPurchases $event)
    {
        // Successfully processed orders
        if (isset($event->dataOrders['orders_ids_successful'])) {
            $orders = GoodsOrder::findMany($event->dataOrders['orders_ids_successful']);

            foreach ($orders as $order) {

                $order->load('goods.city', 'purchase', 'client');

                $telegramMessage = view('telegram.successful_purchase', [
                    'order' => $order
                ])->render();

                // Send message to client in telegram
                try {
                    TgRequest::sendMessage([
                        'chat_id' => $order->client->tg_chatid,
                        'text' => $telegramMessage,
                        'parse_mode' => 'markdown'
                    ]);
                } catch (TelegramException $e) {
                    //var_dump($e);
                    echo $e->getMessage();
                }
            }
        }
    }
}
