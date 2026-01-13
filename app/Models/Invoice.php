<?php

namespace App\Models;

use App\Observers\ActivityObserver;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Invoice extends Model
{



    protected $fillable = [
        'booking_id',
        'total',
        'pickup_dropOff',
        'tanggal_invoice',
        'status',
    ];

    public function booking()
    {
        return $this->belongsTo(Booking::class);
    }
    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }

    public function getTotalDendaAttribute(): float
    {
        return $this->booking?->penalty->sum('amount') ?? 0;
    }

    /**
     * Total tagihan AKTUAL (REAL TIME)
     */
    public function getTotalTagihanAttribute(): float
    {
        $biayaSewa = $this->booking?->estimasi_biaya ?? 0;
        $pickup = $this->pickup_dropOff ?? 0;

        return $biayaSewa + $pickup + $this->total_denda;
    }

    /**
     * Total sudah dibayar
     */
    public function getTotalPaidAttribute(): float
    {
        return $this->payments()->sum('pembayaran');
    }

    /**
     * Sisa pembayaran REAL TIME
     */
    public function getSisaPembayaranAttribute(): float
    {
        return max($this->total_tagihan - $this->total_paid, 0);
    }

    /**
     * Status invoice
     */
    public function getStatusAttribute(): string
    {
        return $this->sisa_pembayaran <= 0 ? 'lunas' : 'belum_lunas';
    }



}
