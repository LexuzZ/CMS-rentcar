<?php

namespace App\Models;

use App\Observers\ActivityObserver;
use Illuminate\Database\Eloquent\Model;

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

}
