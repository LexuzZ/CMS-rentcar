<?php

namespace App\Models;

use App\Observers\ActivityObserver;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Invoice extends Model
{
    protected $appends = [
        'total_tagihan',
        'sisa_pembayaran_hitung',
    ];

    public function getTotalTagihanAttribute(): int
    {
        $biayaSewa = $this->booking?->estimasi_biaya ?? 0;
        $pickup = $this->pickup_dropOff ?? 0;
        $denda = $this->booking?->penalty->sum('amount') ?? 0;

        return $biayaSewa + $pickup + $denda;
    }

    public function getSisaPembayaranHitungAttribute(): int
    {
        return $this->total_tagihan - ($this->dp ?? 0);
    }
    // App\Models\Invoice.php
    public function getTotalTagihan(): int
    {
        $biayaSewa = $this->booking?->estimasi_biaya ?? 0;
        $biayaAntar = $this->pickup_dropOff ?? 0;
        $totalDenda = $this->booking?->penalty->sum('amount') ?? 0;

        return $biayaSewa + $biayaAntar + $totalDenda;
    }
    public function recalculatePaymentStatus(): void
    {
        $sisa = $this->getSisaPembayaran();

        if ($sisa <= 0) {
            $this->payment()
                ->where('status', 'belum_lunas')
                ->update(['status' => 'lunas']);
        } else {
            $this->payment()
                ->where('status', 'lunas')
                ->update(['status' => 'belum_lunas']);
        }
    }


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
