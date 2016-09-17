<?php

namespace App\Ghost\Repositories\Miner;

use App\Ghost\Repositories\Traits\BaseRepoTrait;
use App\Miner as MinerModel;

abstract class Miner
{
    use BaseRepoTrait;

    /**
     * @var MinerModel
     */
    protected $miner;

    public function __construct()
    {
        $this->miner = new MinerModel();
    }

    public function findMiner($miner)
    {
        if (!is_numeric($miner)) {
            return $this->miner->where('name', $miner)->firstOrFail();
        } else {
            return $this->miner->findOrFail($miner);
        }
    }
}