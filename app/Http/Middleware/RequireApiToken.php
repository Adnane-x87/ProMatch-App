<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RequireApiToken
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Accept:
        // 1) API token stored in web session after login,
        // 2) Bearer token from JS calls,
        // 3) Existing authenticated web session user as a fallback.
        $sessionToken = session('api_token');
        $bearerToken = $request->bearerToken();
        $sessionUser = session('user');

        if (empty($sessionToken) && empty($bearerToken) && empty($sessionUser)) {
            return response()->json(['message' => 'Unauthenticated.'], 401);
        }

        return $next($request);
    }
}
