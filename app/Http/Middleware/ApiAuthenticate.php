<?php

namespace App\Http\Middleware;

use Closure;
use Cache;
use App\Ghost\Api\ApiGuard;

class ApiAuthenticate
{
    public $exceptRouteCheckToken = [
        'api/authenticate/check-access-token'
    ];

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next, $role = 'user')
    {
        if (!in_array($request->path(), $this->exceptRouteCheckToken)) {

            if (!$request->has('access_token')) {
                return response()->json(['status' => 'fail', 'message' => 'unauthorized'], 403);
            }

            $apiGuard = new ApiGuard();

            if ($role == 'user') {
                $check = $apiGuard->hasAccessToken($request->input('access_token'));
            } elseif ($role == 'admin') {
                $check = $apiGuard->hasAccessTokenAdmin($request->input('access_token'));
            }

            if (!$check) {
                return response()->json(['status' => 'fail', 'middleware' => true], 401);
            }
        }

        return $next($request);
    }
}
