<?php

namespace App\Ghost\Api\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller as BaseController;
use App\Ghost\Repositories\Client\ClientRepository;
use App\Ghost\Api\ApiResponse;
use App\Ghost\Repositories\Goods\{
    GoodsManager,
    GoodsOrder
};

class BaseApiController extends BaseController
{
    /**
     * @var ClientRepository
     */
    protected $clientRepository;

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
        $this->clientRepository = new ClientRepository();
        $this->apiResponse      = new ApiResponse();
        $this->goodsManager     = new GoodsManager();
        $this->goodsOrder       = new GoodsOrder();
    }

}