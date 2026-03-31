<?php

namespace Modules\Shared\Delivery\Middleware;

use Closure;
use Illuminate\Http\Request;
use Modules\Shared\Infrastructure\Helpers\ApiResponse;

class SignatureMiddleware
{
    /**
     * Handle security verification (Signature/Token) before passing the request to the Controller.
     *
     * @param  \Illuminate\Http\Request  $request  The incoming HTTP request instance.
     * @param  \Closure  $next  The next middleware callback.
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\Response
     */
    public function handle(Request $request, Closure $next)
    {
        $signature = $request->header('X-Signature');
        $expectedSignature = config('3d.security_key');

        // Validate signature presence and correctness
        if (empty($signature) || $signature !== $expectedSignature) {
            return ApiResponse::error('Invalid or missing signature', 403);
        }

        // Proceed to the next middleware/controller if signature is valid
        return $next($request);
    }
}
