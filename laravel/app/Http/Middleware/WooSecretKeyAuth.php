<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class WooSecretKeyAuth
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $webhookSecret = config('services.woocommerce.webhook_secret');
        $header = $request->header('x-wc-webhook-signature');
        $payload = $request->getContent();
        $calculated = hash_hmac('sha256', $payload, $webhookSecret, true);
        $calculated = base64_encode($calculated);

        if ($header !== $calculated) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        return $next($request);
    }
}
