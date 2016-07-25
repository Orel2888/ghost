<?php

namespace App\Ghost\Api;

use Cache;
use Carbon\Carbon;
use App\Admin;

class ApiGuard
{
    public $accessTokenLength = 40;
    public $tokenTimeExpires  = 86400;

    public function authenticate($key)
    {
        if ($key === env('API_KEY')) {
            $accessToken = $this->generateAccessToken();

            $dateExpires = Carbon::now()->addDay();

            $dataAccessToken = [
                'access_token' => $accessToken,
                'expires_date' => $dateExpires->toDateTimeString(),
                'expires_unix' => $dateExpires->timestamp
            ];

            Cache::put('api_token_'. $accessToken, $dataAccessToken, $dateExpires);

            return $dataAccessToken;
        }

        return false;
    }

    public function authenticateAdmin($key, $tgUsername)
    {
        if (!$auth = $this->authenticate($key)) {
            return false;
        }

        if (is_null(Admin::whereLogin($tgUsername)->first())) {
            return false;
        }

        Cache::put('api_admin_token_'. $auth['access_token'], $auth, $auth['expires_unix']);

        return $auth;
    }

    public function hasAccessToken($token)
    {
        return Cache::has('api_token_'. $token);
    }

    public function hasAccessTokenAdmin($token)
    {
        return Cache::has('api_admin_token_'. $token);
    }

    public function getInfoAccessToken($token)
    {
        return $this->hasAccessToken($token) ? Cache::get('api_token_'. $token) : false;
    }

    public function generateAccessToken()
    {
        return bin2hex(openssl_random_pseudo_bytes($this->accessTokenLength));
    }
}