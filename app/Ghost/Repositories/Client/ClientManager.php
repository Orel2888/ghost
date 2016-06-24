<?php

namespace App\Ghost\Repositories\Client;

class ClientManager extends Client
{

    /**
     * @throw Illuminate\Database\Eloquent\ModelNotFoundException
     * @param $tgChatId
     * @return mixed
     */
    public function findByTgChatId($tgChatId)
    {
        return $this->client->whereTgChatid($tgChatId)->firstOrFail();
    }
}