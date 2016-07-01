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

    public function postAuthenticate($tgUsername = null)
    {
        $valid = Validator::make($this->request->all(), [
            'key'   => 'required|alpha_num'
        ]);

        if ($valid->fails()) {
            return response()->json($this->apiResponse->error($valid->messages()->getMessages()), 400);
        }

        if (!is_null($tgUsername)) {
            // Authenticate admin
            if (!$auth = $this->apiGuard->authenticateAdmin($this->request->input('key'), $tgUsername)) {
                return response()->json($this->apiResponse->fail(['message' => 'Unauthorized']), 401);
            }
        } else {
            if (!$auth = $this->apiGuard->authenticate($this->request->input('key'))) {
                return response()->json($this->apiResponse->fail(['messages' => 'Unauthorized']), 401);
            }
        }
        
        return response()->json($this->apiResponse->ok($auth));
    }

    public function postCheckAccessToken()
    {
        $valid = Validator::make($this->request->all(), [
            'access_token'  => 'required'
        ]);

        if ($valid->fails()) {
            return response()->json($this->apiResponse->error($valid->messages()->getMessages()), 400);
        }

        if ($this->apiGuard->hasAccessToken($this->request->input('access_token'))) {
            return response()->json($this->apiResponse->ok());
        } else {
            return response()->json($this->apiResponse->fail());
        }
    }
}