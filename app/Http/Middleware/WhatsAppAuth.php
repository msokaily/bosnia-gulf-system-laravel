<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Auth\Middleware\Authenticate as Middleware;

class WhatsAppAuth extends Middleware
{
    public function handle($request, Closure $next, ...$guards)
    {
        if (!$request->isJson() || !$request->is('api/*') || $request->input('hub_verify_token') != env('WHATSAPP_API_TOKEN'))
        {
            return response()->json([
                'status' => false,
                'message' => 'Unauthrozed'
            ], 401);
        }
        dd('Else: '.$request->isJson());
        return parent::handle($request, $next, ...$guards);
    }
}
