<?php

namespace Patchub\Client\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class VerifySignature
{
    public function handle(Request $request, Closure $next): Response
    {
        $secret = config('patchub-client.webhook_secret');

        if (! $secret) {
            abort(500, 'Patchub: PATCHUB_WEBHOOK_SECRET is not configured.');
        }

        $signature = $request->header('X-Patchub-Signature');

        if (! $signature) {
            abort(401, 'Patchub: missing signature.');
        }

        $expectedSignature = hash_hmac('sha256', $request->getContent(), $secret);

        if (! hash_equals($expectedSignature, $signature)) {
            abort(401, 'Patchub: invalid signature.');
        }

        return $next($request);
    }
}
