<?php

namespace Ghanem\Dtone\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class VerifyWebhookSignature
{
    /**
     * Handle an incoming request.
     *
     * @param Request $request
     * @param Closure $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        $secret = config('dtone.webhook_secret');

        if (empty($secret)) {
            return $next($request);
        }

        $signature = $request->header('X-Dtone-Signature');

        if (empty($signature)) {
            abort(403, 'Missing webhook signature.');
        }

        $computed = hash_hmac('sha256', $request->getContent(), $secret);

        if (! hash_equals($computed, $signature)) {
            abort(403, 'Invalid webhook signature.');
        }

        return $next($request);
    }
}
