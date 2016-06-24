<?php

namespace App\Ghost\Api\Controllers;

use Validator;
use App\Ghost\Api\ApiGuard;

class AuthenticateApiController extends BaseApiController
{
    /**
     * @var ApiGuard
     */
    protected $apiGuard;

    public function __construct()
    {
        parent::__construct();

        $this->apiGuard = new ApiGuard();
    }

    public function postAuthenticate()
    {
        $valid = Validator::make($this->request->all(), [
            'key'   => 'required|alpha_num'
        ]);

        if ($valid->fails()) {
            return response()->json($this->apiResponse->error($valid->messages()->getMessages()), 400);
        }

        if (!$auth = $this->apiGuard->authenticate($this->request->input('key'))) {
            return response()->json($this->apiResponse->fail(['messages' => 'Unauthorized']), 401);
        }

        return response()->json($this->apiResponse->ok($auth));
    }
}