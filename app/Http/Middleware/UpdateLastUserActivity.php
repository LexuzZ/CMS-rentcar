<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class UpdateLastUserActivity
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        if (Auth::check()) {
            $user = Auth::user();

            if (
                !$user->last_seen_at ||
                $user->last_seen_at->lt(now()->subMinutes(2))
            ) {
                $user->forceFill([
                    'last_seen_at' => now(),
                ])->save();
            }
        }


        return $response;
    }
}
