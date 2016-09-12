<?php

namespace App\Ghost\Domains\Miner\Entities;

use App\Ghost\Domains\Miner\MinerInfoDataProvider;
use Illuminate\Support\Collection;

class MinerInfo extends Entity
{
    public $collection;

    public function __construct(Collection $collection)
    {
        $this->collection = $collection;
    }
}