<?php

namespace App\Ghost\Api\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller as BaseController;
use App\Ghost\Repositories\Client\ClientManager;
use App\Ghost\Api\ApiResponse;

class BaseApiController extends BaseController
{
    /**
     * @var Request
     */
    protected $request;

    /**
     * @var ClientManager
     */
    protected $clientManager;

    /**
     * @var ApiResponse
     */
    protected $apiResponse;

    public function __construct()
    {
        $this->request       = app('request');
        $this->clientManager = new ClientManager();
        $this->apiResponse   = new ApiResponse();
    }

}