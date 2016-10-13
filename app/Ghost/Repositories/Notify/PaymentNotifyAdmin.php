<?php

namespace App\Ghost\Notify;

class PaymentNotifyAdmin extends Notify
{

    /**
     * Instance
     * @var \Longman\TelegramBot\Telegram
     */
    public $tg;

    /**
     * @var array
     */
    public $receivedData = [];

    public function __construct(array $eventData)
    {
        $this->tg = app('tgbot');
        $this->receivedData = $eventData;
    }

    public function run()
    {
        $admins = $this->getAdmins();


    }

    public function sendNotify()
    {

    }
}