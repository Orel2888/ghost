<?php

namespace App\Listeners;

use App\Events\QiwiTransaction;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Longman\TelegramBot\Request as TgRequest;
use App\Admin;

class NotifyAdminTelegramAbuseTransaction
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
     * @param  QiwiTransaction  $event
     * @return void
     */
    public function handle(QiwiTransaction $event)
    {
        //
        if (!empty($event->transactions)) {

            $admins = Admin::where('tg_chatid', '>', 0)->whereTgNotifyQtrans(1)->get();

            foreach ($admins as $admin) {

                $sep = str_repeat('❄️', 10);
                $notifyMessage = view('telegram.admin_abuse_qiwitrans', [
                    'transactions' => $event->transactions,
                    'sep' => $sep
                ])->render();

                try {
                    TgRequest::sendMessage([
                        'chat_id'       => $admin->tg_chatid,
                        'text'          => $notifyMessage,
                        'parse_mode'    => 'markdown'
                    ]);
                } catch (TelegramException $e) {
                    echo $e->getMessage();
                }
            }
        }
    }
}
