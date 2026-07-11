<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LoginActivity extends Model
{
    protected $fillable = [
        'user_id',
        'ip_address',
        'user_agent',
        'device',
        'browser',
        'platform',
        'status',
        'logged_in_at',
    ];

    protected $casts = [
        'logged_in_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /* ── Helper: parse user agent ── */
    public static function parseUserAgent(?string $ua): array
    {
        if (!$ua) return ['device' => 'Unknown', 'browser' => 'Unknown', 'platform' => 'Unknown'];

        // Platform
        $platform = 'Unknown';
        if (str_contains($ua, 'Windows'))      $platform = 'Windows';
        elseif (str_contains($ua, 'Macintosh')) $platform = 'macOS';
        elseif (str_contains($ua, 'Linux'))     $platform = 'Linux';
        elseif (str_contains($ua, 'Android'))   $platform = 'Android';
        elseif (str_contains($ua, 'iPhone') || str_contains($ua, 'iPad')) $platform = 'iOS';

        // Browser
        $browser = 'Unknown';
        if (str_contains($ua, 'Edg'))           $browser = 'Edge';
        elseif (str_contains($ua, 'OPR') || str_contains($ua, 'Opera')) $browser = 'Opera';
        elseif (str_contains($ua, 'Chrome'))    $browser = 'Chrome';
        elseif (str_contains($ua, 'Firefox'))   $browser = 'Firefox';
        elseif (str_contains($ua, 'Safari'))    $browser = 'Safari';

        // Device
        $device = 'Desktop';
        if (str_contains($ua, 'Mobile'))        $device = 'Mobile';
        elseif (str_contains($ua, 'Tablet') || str_contains($ua, 'iPad')) $device = 'Tablet';

        return compact('device', 'browser', 'platform');
    }
}
