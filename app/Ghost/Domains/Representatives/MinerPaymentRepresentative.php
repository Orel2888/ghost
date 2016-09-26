<?php

namespace App\Ghost\Domains\Representatives;

use App\MinerPayment;

class MinerPaymentRepresentative
{
    public $model;

    public function __construct(MinerPayment $model)
    {
        $this->model = $model;
    }


}