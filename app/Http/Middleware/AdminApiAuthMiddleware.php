<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class AdminApiAuthMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        \Illuminate\Support\Facades\Log::info('AdminApiAuthMiddleware: Checking authentication');

        if (!Auth::check()) {
            \Illuminate\Support\Facades\Log::warning('AdminApiAuthMiddleware: User not authenticated');
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized',
                'redirect' => route('admin.login')
            ], 401);
        }

        $user = Auth::user();
        \Illuminate\Support\Facades\Log::info('AdminApiAuthMiddleware: User authenticated', [
            'user_id' => $user->id,
            'user_name' => $user->name,
            'is_admin' => $user->is_admin
        ]);

        if (!$user->is_admin) {
            \Illuminate\Support\Facades\Log::warning('AdminApiAuthMiddleware: User is not admin', [
                'user_id' => $user->id,
                'user_name' => $user->name
            ]);
            Auth::logout();
            return response()->json([
                'success' => false,
                'message' => 'Access denied. Admin privileges required.',
                'redirect' => route('admin.login')
            ], 403);
        }

        \Illuminate\Support\Facades\Log::info('AdminApiAuthMiddleware: Admin access granted');
        $response = $next($request);

        // Add cache control headers to prevent browser caching
        $response->headers->set('Cache-Control', 'no-cache, no-store, must-revalidate, max-age=0');
        $response->headers->set('Pragma', 'no-cache');
        $response->headers->set('Expires', '0');
        $response->headers->set('X-Frame-Options', 'DENY');
        $response->headers->set('X-Content-Type-Options', 'nosniff');
        $response->headers->set('X-XSS-Protection', '1; mode=block');

        return $response;
    }
}
