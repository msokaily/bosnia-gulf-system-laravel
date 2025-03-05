<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Laravel\Sanctum\PersonalAccessToken;

class SanctumTokenAuth
{
    public function handle(Request $request, Closure $next)
    {
        // Check if "api_token" is provided in the URL
        $token = $request->query('api_token', $request->bearerToken());

        if ($token) {
            $accessToken = PersonalAccessToken::findToken($token);

            if ($accessToken) {
                $request->setUserResolver(fn () => $accessToken->tokenable);
            }
        }

        return $next($request);
    }
}
