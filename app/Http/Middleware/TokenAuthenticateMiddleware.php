<?php

namespace App\Http\Middleware;

use App\Models\ApiToken;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class TokenAuthenticateMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param Closure(Request): (Response) $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Check if the user is authenticated
        if ($request->header()['authorization'][0] && ApiToken::where('api_key', $request->header()['authorization'][0] ?? '')->exists()) {
            return $next($request);
        } else {
            // If not authenticated, redirect to login or send an unauthorized response
            return response()->json([
                'message' => 'You are not authorized to access this endpoint.'
            ], 401);
        }
    }
}
