<?php

namespace App\Listeners;

use App\Events\WasPurchases;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Admin;
use Longman\TelegramBot\Request as TgRequest;
use Longman\TelegramBot\Exception\TelegramException;

class NotifyAdminToTelegram
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
        //
        $admins = Admin::whereTgNotifyPurchase(1)->where('tg_chatid', '>', 0)->get();

        if (!empty($event->orders)) {

            $sep = str_repeat('🍃', 10);
            $notifyMessage = view('telegram.admin_about_purchase', ['orders' => $event->orders, 'sep' => $sep])->render();

            foreach ($admins as $admin) {

                try {
                    TgRequest::sendMessage([
                        'chat_id'       => $admin->tg_chatid,
                        'text'          => $notifyMessage,
                        'parse_mode'    => 'markdown'
                    ]);
                } catch (TelegramException $e) {
                    //dump($notifyMessage);
                    echo $e->getMessage();
                }
            }
        }
    }
}
