<?php

namespace App\Models;

use App\Observers\ActivityObserver;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Driver extends Model
{
    protected $fillable = [
        'nama',
        'no_telp',
        'status',
        'harga',
    ];
    protected static function booted()
    {
        static::observe(ActivityObserver::class);
    }
    public function antar()
    {
        return $this->hasMany(Booking::class, 'driver_pengantaran_id');
    }
    public function jemput()
    {
        return $this->hasMany(Booking::class, 'driver_pengembalian_id');
    }



}
