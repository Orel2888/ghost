<?php

namespace App\Ghost\Repositories\Tg;

use App\Ghost\Repositories\Tg\Channels\TgChannelSync;
use Longman\TelegramBot\Request as TgRequest;
use Longman\TelegramBot\Exception\TelegramException;
use Longman\TelegramBot\Entities\ServerResponse;
use App\Client;

class Tg
{
    public $exception;
    public $notifyChannels = [
        'sync'  => \App\Ghost\Repositories\Tg\Channels\TgChannelSync::class,
        'file'  => \App\Ghost\Repositories\Tg\Channels\TgChannelFile::class
    ];
    public $notifyChannel;

    public $forceTest = false;

    public function __construct($notifyChannel = 'sync')
    {
        $this->setNotifyChannel($notifyChannel);
    }

    public function sendNews($messageData)
    {
        TgRequest::sendMessage($messageData);
    }

    public function setNotifyChannel($value)
    {
        if (!isset($this->notifyChannels[$value])) throw new \Exception('Channel '. $value .' not found');

        $this->notifyChannel = new $this->notifyChannels[$value]();
    }

    public function catchException($e)
    {
        var_dump($e->getMessage());

        $this->notifyChannel->pushOn('error', $e);
    }

    public function getClientsTesting()
    {
        $clientsUsername = explode(',', config('shop.test_telegram_clients'));

        if (empty($clientsUsername)) {
            throw new Exception("Telegram clients for testing not found");
        }

        $clients = array_map(function ($username) {
            return Client::where('tg_username', $username)->first();
        }, $clientsUsername);

        $clients = array_filter($clients, function ($item) {
            return !is_null($item);
        });

        return collect($clients);
    }

    public function getClientChatIds()
    {
        if (env('APP_ENV') == 'testing' || $this->forceTest) {
            $clients = $this->getClientsTesting();
        } else {
            $clients = Client::select('id', 'tg_chatid', 'name')->whereNotify(1)->get();
        }

        return $clients;
    }

    public function countClientForNotify()
    {
        return Client::whereNotify(1)->count();
    }

    public function handleAttributes($clientCollection, $message)
    {
        return strtr($message, [
            ':nick' => $clientCollection->name
        ]);
    }

    public function newsletter($message)
    {
        $clients = $this->getClientChatIds();

        $count     = 0;
        $countOk   = 0;
        $countFail = 0;

        $this->notifyChannel->pushOn('send_newsletter_before', ['count_clients' => $clients->count()]);

        foreach ($clients as $client) {
            $send = $this->sendMessageToUser($client->tg_chatid, $this->handleAttributes($client, $message));

            $count++;

            if ($send instanceof ServerResponse) {
                if ($send->isOk()) {
                    $countOk++;
                } else {
                    $countFail++;
                }
            }

            $notifyEventData = [
                'offset_send'   => $countOk,
                'count_fail'    => $countFail,
                'response'      => $send
            ];

            $this->notifyChannel->pushOn('send_newsletter_step', $notifyEventData);
        }

        if ($count == $clients->count()) {
            $this->notifyChannel->pushOn('send_newsletter_complete', $notifyEventData);
        }
    }

    public function sendMessageToUser($id, $message, $modes = ['parse_mode' => 'markdown'])
    {
        $attributes = [
            'chat_id'   => $id,
            'text'      => $message
        ];

        $attributes += $modes;

        try {
            return TgRequest::sendMessage($attributes);
        } catch (TelegramException $e) {
            $this->catchException($e);
        }
    }

    public function emitChannel($name, $data = [])
    {
        if (isset($this->notifyChannels[$name])) {
            return new $this->notifyChannels[$name]($data);
        } else {
            throw new \Exception('Driver for '. $name .' not found');
        }
    }

    public function subscribe($name, $callback)
    {
        $this->notifyChannels[$name] = 0;
    }

    public function forceTest()
    {
        return $this->forceTest = true;
    }
}