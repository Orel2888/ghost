<?php

namespace App\Ghost\Api\Controllers;

use App\QiwiTransaction;

class AdminApiController extends BaseApiController
{
    public function getQiwiTransaction()
    {
        $transactions = QiwiTransaction::orderBy('id', 'DESC')->orderBy('id', 'ASC')->limit(10)->all();

        return response()->json($this->apiResponse->ok(['data' => $transactions->toArray()]));
    }
}