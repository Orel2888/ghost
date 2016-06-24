<?php

namespace App\Ghost\Repositories\Client;

use App\Client as ClientModel;

abstract class Client
{
    /**
     * @var ClientModel
     */
    protected $client;

    public function __construct()
    {
        $this->client = new ClientModel();
    }
}