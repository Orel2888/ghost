<?php

namespace App\Ghost\Apanel\Controllers;

use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use App\Ghost\Repositories\Goods\GoodsManager;

class ApanelBaseController extends BaseController
{
    use ValidatesRequests;

    /**
     * @var Request
     */
    protected $request;

    /**
     * @var GoodsManager
     */
    protected $goodsManager;

    public function __construct()
    {
        $this->request      = app('request');
        $this->goodsManager = new GoodsManager();
    }
}