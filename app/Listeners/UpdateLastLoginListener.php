<?php

namespace App\Listeners;

use App\Models\LoginActivity;
use Illuminate\Auth\Events\Login;
use Illuminate\Auth\Events\Failed;

class UpdateLastLoginListener
{
    public function handle(Login $event): void
    {
        $ua      = request()->userAgent();
        $parsed  = LoginActivity::parseUserAgent($ua);

        // Update kolom last_login di tabel users
        $event->user->forceFill([
            'last_login_at' => now(),
            'last_login_ip' => request()->ip(),
        ])->save();

        // Catat ke tabel login_activities
        LoginActivity::create([
            'user_id'      => $event->user->id,
            'ip_address'   => request()->ip(),
            'user_agent'   => $ua,
            'device'       => $parsed['device'],
            'browser'      => $parsed['browser'],
            'platform'     => $parsed['platform'],
            'status'       => 'success',
            'logged_in_at' => now(),
        ]);
    }
}

// ── Listener untuk login gagal ──
// Daftarkan juga class ini untuk event Failed jika ingin melacak percobaan login gagal

class RecordFailedLoginListener
{
    public function handle(Failed $event): void
    {
        $ua     = request()->userAgent();
        $parsed = LoginActivity::parseUserAgent($ua);

        LoginActivity::create([
            'user_id'      => $event->user?->id,
            'ip_address'   => request()->ip(),
            'user_agent'   => $ua,
            'device'       => $parsed['device'],
            'browser'      => $parsed['browser'],
            'platform'     => $parsed['platform'],
            'status'       => 'failed',
            'logged_in_at' => now(),
        ]);
    }
}
