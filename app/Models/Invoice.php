<?php

namespace App\Models;

use App\Observers\ActivityObserver;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Invoice extends Model
{
    protected $fillable = [
        'booking_id',
        'total',
        'dp',
        'sisa_pembayaran',
        'pickup_dropOff',
        'tanggal_invoice',
    ];

    public function booking()
    {
        return $this->belongsTo(Booking::class);
    }
     public function payment(): HasOne
    {
        return $this->hasOne(Payment::class);
    }
    protected static function booted()
    {
        static::observe(ActivityObserver::class);
    }


}
