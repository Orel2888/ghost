<?php

namespace App\Ghost\Repositories\Client;

use App\Client as ClientModel;
use App\Ghost\Repositories\Goods\GoodsOrder as GoodsOrderRepo;
use Illuminate\Database\Eloquent\ModelNotFoundException;

abstract class Client
{
    /**
     * @var ClientModel
     */
    protected $clientModel;

    /**
     * @var GoodsOrderRepo
     */
    protected $goodsOrderRepo;

    public function __construct()
    {
        $this->clientModel = new ClientModel();
        $this->goodsOrderRepo = new GoodsOrderRepo();
    }

    /**
     * @throw Illuminate\Database\Eloquent\ModelNotFoundException
     * @param int $tgChatId
     * @return mixed
     */
    public function findByTgChatId(int $tgChatId)
    {
        return $this->clientModel->whereTgChatid($tgChatId)->firstOrFail();
    }

    /**
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException
     * @param int $clientId
     * @return mixed
     */
    public function find(int $clientId)
    {
        return $this->clientModel->findOrFail($clientId);
    }
}