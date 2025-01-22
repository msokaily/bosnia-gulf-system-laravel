<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Auth\Middleware\Authenticate as Middleware;

class WhatsAppAuth extends Middleware
{
    public function handle($request, Closure $next, ...$guards)
    {
        if ($request->input('hub_verify_token') != env('WHATSAPP_API_ACCESS'))
        {
            return response()->json([
                'status' => false,
                'message' => 'Unauthrozed'
            ], 401);
        }
        return $next($request);
    }
}
