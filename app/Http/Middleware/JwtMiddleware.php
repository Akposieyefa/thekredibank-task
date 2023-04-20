<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Symfony\Component\HttpFoundation\Response as HttpResponse;

class JwtMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param Request $request
     * @param Closure(Request): (Response|RedirectResponse) $next
     * @return JsonResponse
     */
    public function handle(Request $request, Closure $next): JsonResponse
    {
        try {
            $user = auth()->userOrFail();
        } catch (\PHPOpenSourceSaver\JWTAuth\Exceptions\UserNotDefinedException $e) {
            return response()->json([
                'message' => 'Unauthenticated',
                'success' => false
            ],HttpResponse::HTTP_UNAUTHORIZED);
        }
        return $next($request);
    }
}
