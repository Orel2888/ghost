<?php

namespace App\Ghost\Api\Controllers;

use App\QiwiTransaction;

class AdminApiController extends BaseApiController
{
    public function getQiwiTransaction()
    {
        $transactions = QiwiTransaction::orderBy('id', 'DESC')->limit(10)->get()->reverse();

        return response()->json($this->apiResponse->ok(['data' => $transactions->toArray()]));
    }
}