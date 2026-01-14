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
        'pickup_dropOff',
        'total_tagihan',
        'total_denda',
        'total_paid',
        'sisa_pembayaran',
        'status',
        'tanggal_invoice',
    ];
    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }

    /**
     * Hitung ulang semua nilai invoice
     */
    public function recalculate(): void
    {
        $booking = $this->booking;

        $biayaSewa = $booking?->estimasi_biaya ?? 0;
        $pickup = $this->pickup_dropOff ?? 0;

        $totalDenda = $booking?->penalty()->sum('pembayaran') ?? 0;
        $totalPaid = $this->payments()->sum('pembayaran');

        $totalTagihan = $biayaSewa + $pickup + $totalDenda;
        $sisa = max($totalTagihan - $totalPaid, 0);

        $this->updateQuietly([
            'total_tagihan' => $totalTagihan,
            'total_denda' => $totalDenda,
            'total_paid' => $totalPaid,
            'sisa_pembayaran' => $sisa,
            'status' => $sisa === 0 ? 'lunas' : 'belum_lunas',
        ]);
    }
}

