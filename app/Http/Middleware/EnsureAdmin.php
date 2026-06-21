<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class EnsureAdmin
{
    /**
     * Handle an incoming request.
     * Only allow users with user_type 'admin'.
     *
     * - API requests → JSON 403
     * - Web, unauthenticated → redirect to login
     * - Web, authenticated but not admin → abort 403 (no redirect loop)
     */
    public function handle(Request $request, Closure $next)
    {
        $user = $request->user();

        // Not logged in at all → send to login
        if (! $user) {
            if ($request->expectsJson()) {
                return response()->json(['status' => false, 'message' => 'Unauthenticated.'], 401);
            }
            return redirect()->route('login');
        }

        // Logged in but not admin → hard 403, no redirect (prevents redirect loops)
        if (strtolower($user->user_type ?? '') !== 'admin') {
            if ($request->expectsJson()) {
                return response()->json(['status' => false, 'message' => 'Unauthorized. Admin access only.'], 403);
            }
            abort(403, 'Access Denied. This area is for administrators only.');
        }

        return $next($request);
    }
}
