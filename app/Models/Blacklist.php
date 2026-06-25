<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Blacklist extends Model
{
    protected $fillable = [
        'nik',
        'nama',
        'alasan',
        'catatan',
        'blacklisted_by',
        'blacklisted_at',
    ];

    protected $casts = [
        'blacklisted_at' => 'datetime',
    ];

    /**
     * Cek apakah NIK tertentu masuk blacklist.
     */
    public static function isBlacklisted(string $nik): bool
    {
        return static::where('nik', $nik)->exists();
    }

    /**
     * Ambil data blacklist berdasarkan NIK.
     */
    public static function findByNik(string $nik): ?static
    {
        return static::where('nik', $nik)->first();
    }
}
