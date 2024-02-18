<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Auth\Middleware\Authenticate as Middleware;
use Illuminate\Http\Request;

class Authenticate extends Middleware
{
    public function handle($request, Closure $next, ...$guards)
    {
        if ($request->isJson() || $request->is('api/*'))
        {
            if (!auth($guards[0])->check()) {
                return response()->json([
                    'status' => false,
                    'message' => 'Unauthrozed'
                ], 401);
            }
        }
        return parent::handle($request, $next, ...$guards);
    }

    /**
     * Get the path the user should be redirected to when they are not authenticated.
     */
    // protected function redirectTo(Request $request): ?string
    // {
    //     return ($request->expectsJson() || explode('/', $request->getRequestUri())[1] == 'api') ? response()->json(['status' => false]) : route('home');
    // }

    /**
     * Convert an authentication exception into a response.
     * Return 401 if unauthenticated API.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Illuminate\Auth\AuthenticationException  $exception
     * @return \Symfony\Component\HttpFoundation\Response
     */

    // protected function unauthenticated($request, $guards)
    // {
    //     http_response_code(404);
    //     // header('Accept: application/json; Content-Type: application/json; charset=utf-8; status');
    //     echo json_encode(['status' => false]);
    // }
}
