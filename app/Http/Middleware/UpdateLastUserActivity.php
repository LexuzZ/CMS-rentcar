<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Symfony\Component\HttpFoundation\Response;

class UpdateLastUserActivity
{
     protected int $dbUpdateInterval = 5;

    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        // Hanya untuk user login
        if (!Auth::check()) {
            return $response;
        }

        // ❌ Abaikan request Livewire / AJAX / polling
        if ($request->ajax() || $request->is('livewire/*')) {
            return $response;
        }

        $user = Auth::user();

        /**
         * ===============================
         * 1️⃣ Simpan aktivitas ke CACHE
         * ===============================
         * Cache jauh lebih murah daripada DB
         */
        Cache::put(
            'user_last_seen_' . $user->id,
            now(),
            now()->addMinutes(10)
        );

        /**
         * ===============================
         * 2️⃣ Update DB hanya jika perlu
         * ===============================
         */
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
