<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Attendance extends Model
{
    protected $fillable = [
        'user_id',
        'date',
        'check_in_time',
        'latitude',
        'longitude',
        'distance_meters',
        'status',
        'note',
        'ip_address',
        'user_agent',
    ];

    protected $casts = [
        'date'     => 'date',
        'latitude'  => 'decimal:7',
        'longitude' => 'decimal:7',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Hitung jarak dua koordinat dalam meter (Haversine formula)
     */
    public static function calculateDistance(
        float $lat1, float $lon1,
        float $lat2, float $lon2
    ): float {
        $earthRadius = 6371000; // meter
        $dLat = deg2rad($lat2 - $lat1);
        $dLon = deg2rad($lon2 - $lon1);
        $a = sin($dLat / 2) ** 2
           + cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * sin($dLon / 2) ** 2;
        return $earthRadius * 2 * atan2(sqrt($a), sqrt(1 - $a));
    }

    /**
     * Apakah user sudah absen hari ini?
     */
    public static function alreadyCheckedIn(int $userId): bool
    {
        return static::where('user_id', $userId)
            ->where('date', today())
            ->exists();
    }

    public function getStatusColorAttribute(): string
    {
        return match ($this->status) {
            'hadir'    => 'success',
            'terlambat'=> 'warning',
            'izin'     => 'info',
            'alpha'    => 'danger',
            default    => 'gray',
        };
    }

    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            'hadir'    => 'Hadir',
            'terlambat'=> 'Terlambat',
            'izin'     => 'Izin',
            'alpha'    => 'Alpha',
            default    => ucfirst($this->status),
        };
    }
}
