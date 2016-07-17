<?php

namespace App\Ghost\Api\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller as BaseController;
use App\Ghost\Repositories\Client\ClientManager;
use App\Ghost\Api\ApiResponse;
use App\Ghost\Repositories\Goods\GoodsManager;
use App\Ghost\Repositories\Goods\GoodsOrder;

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

    /**
     * @var GoodsManager
     */
    protected $goodsManager;

    /**
     * @var GoodsOrder
     */
    protected $goodsOrder;

    public function __construct()
    {
        $this->request       = app('request');
        $this->clientManager = new ClientManager();
        $this->apiResponse   = new ApiResponse();
        $this->goodsManager  = new GoodsManager();
        $this->goodsOrder    = new GoodsOrder();
    }

}