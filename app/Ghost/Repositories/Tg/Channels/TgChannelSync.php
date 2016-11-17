<?php

namespace App\Ghost\Repositories\Tg\Channels;

class TgChannelSync
{
    public $totalSend;
    public $statSend = [];

    public function __construct()
    {

    }

    public function handleBefore($data)
    {
        $this->totalSend = $data['count_clients'];

        echo PHP_EOL . 'Началась рассылка...'. PHP_EOL;
        echo 'Количество клиентов: '. $data['count_clients'] . PHP_EOL;
    }

    public function handle($data)
    {
        $this->statSend = $data;

        echo 'Отправлено '. $data['offset_send'] .' из '. $this->totalSend . PHP_EOL;
    }

    public function handleAfter($data)
    {
        echo 'Рассылка закончена! '. PHP_EOL;
        echo 'Успешно отправлено '. $data['offset_send'] .' ошибок '. $data['count_fail'] . PHP_EOL;
    }

    public function handleError($error)
    {
        echo 'Произошла ошибка:'. PHP_EOL;
        //var_dump($error);
    }

    public function pushOn($event, $data)
    {
        if ($event == 'send_newsletter_before') {
            $this->handleBefore($data);
        }

        if ($event == 'send_newsletter_step') {
            $this->handle($data);
        }

        if ($event == 'send_newsletter_complete') {
            $this->handleAfter($data);
        }

        if ($event == 'error') {
            $this->handleError($data);
        }
    }
}