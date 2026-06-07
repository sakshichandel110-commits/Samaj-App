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
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        $user = $request->user();
        if (! $user || strtolower(($user->user_type ?? '')) !== 'admin') {
            return response()->json(['status' => false, 'message' => 'Unauthorized. Only admin users can perform this action.'], 403);
        }

        return $next($request);
    }
}
