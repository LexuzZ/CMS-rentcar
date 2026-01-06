<?php

namespace App\Models;

use App\Observers\ActivityObserver;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Invoice extends Model
{

    protected $appends = [
        'total_tagihan',
        'total_dibayar',
        'sisa_pembayaran',
        'status_pembayaran',
    ];



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
    public function payment(): HasMany
    {
        return $this->hasMany(Payment::class);
    }
    protected static function booted()
    {
        static::observe(ActivityObserver::class);
    }
    public function getTotalTagihanAttribute(): int
    {
        return
            ($this->booking?->estimasi_biaya ?? 0)
            + ($this->pickup_dropOff ?? 0)
            + ($this->booking?->penalty->sum('amount') ?? 0);
    }

    public function getTotalDibayarAttribute(): int
    {
        return $this->payment()->sum('pembayaran');
    }

    public function getSisaPembayaranAttribute(): int
    {
        return max($this->total_tagihan - $this->total_dibayar, 0);
    }

    public function getStatusPembayaranAttribute(): string
    {
        return $this->sisa_pembayaran <= 0 ? 'lunas' : 'belum_lunas';
    }
    public function getTotalTagihan(): float
    {
        return (float) (
            $this->booking->total_harga +
            $this->booking->penalties()->sum('nominal')
        );
    }

    public function getTotalDibayar(): float
    {
        return (float) $this->payments()->sum('pembayaran');
    }

    public function refreshPaymentStatus(): void
    {
        $totalTagihan = $this->getTotalTagihan();
        $totalDibayar = $this->getTotalDibayar();

        $status = $totalDibayar >= $totalTagihan
            ? 'lunas'
            : 'belum_lunas';

        $this->payments()->update([
            'status' => $status,
        ]);
    }


}
