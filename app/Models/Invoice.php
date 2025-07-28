<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

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
    public function payment()
    {
        return $this->belongsTo(Payment::class);
    }
    
    
}
