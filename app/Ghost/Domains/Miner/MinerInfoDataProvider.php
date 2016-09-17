<?php

namespace App\Ghost\Domains\Miner;

use App\Ghost\Repositories\Miner\MinerManager;
use App\Ghost\Domains\Miner\Entities\MinerInfo;
use App\Miner;

class MinerInfoDataProvider
{
    /**
     * @var MinerManager
     */
    public $minerManager;

    /**
     * @var Miner
     */
    public $minerModel;

    public function __construct()
    {
        $this->minerManager = new MinerManager();
        $this->minerModel = new Miner();
    }

    public function mainStat($minerId)
    {
        $miner = $this->minerManager->findMiner($minerId);

        $data = $miner->getAttributes();

        $data['pending_balance']                = round(($miner->counter_goods + $miner->counter_goods_ok) * $miner->ante, 2);
        $data['counter_goods_fail_percent']     = round($miner->counter_goods_ok * $miner->counter_goods_fail / 100, 2);
        $data['create_at']                      = $miner->created_at->format('d.m.Y H:i:s');

        return new MinerInfo(collect($data));
    }
}