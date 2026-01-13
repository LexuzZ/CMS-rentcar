<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Symfony\Component\HttpFoundation\Response;

class UpdateLastUserActivity
{
    /**
     * Interval update ke database (dalam menit)
     */
    protected int $dbUpdateInterval = 10;

    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        if (!Auth::check()) {
            return $response;
        }

        // ✅ BENAR: aman untuk Livewire
        if (
            $request->routeIs('livewire.*') ||
            $request->expectsJson() ||
            $request->wantsJson()
        ) {
            return $response;
        }

        $user = Auth::user();

        Cache::put(
            'user_last_seen_' . $user->id,
            now(),
            now()->addMinutes(10)
        );

        if (
            !$user->last_seen_at ||
            $user->last_seen_at->lt(now()->subMinutes($this->dbUpdateInterval))
        ) {
            $user->forceFill([
                'last_seen_at' => now(),
            ])->save();
        }

        return $response;
    }

}
