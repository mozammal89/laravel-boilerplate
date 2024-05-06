<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class JWTAuthenticateMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param Closure(Request): (Response) $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Check if the user is authenticated
        if (Auth::guard('api')->check()) {
            return $next($request);
        } else {
            // If not authenticated, redirect to login or send an unauthorized response
            return response()->json([
                'message' => 'You are not authorized to access this endpoint.'
            ], 401);
        }
    }
}
