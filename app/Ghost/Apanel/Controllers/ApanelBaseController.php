<?php

namespace App\Ghost\Apanel\Controllers;

use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use App\Ghost\Repositories\Goods\GoodsManager;
use App\Ghost\Apanel\Repository\ApanelRepository;

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

    /**
     * @var ApanelRepository
     */
    protected $apanelRepo;

    public function __construct()
    {
        $this->request      = app('request');
        $this->goodsManager = new GoodsManager();
        $this->apanelRepo   = new ApanelRepository();
    }
}