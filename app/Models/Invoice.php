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
    public function booking()
    {
        return $this->belongsTo(Booking::class);
    }
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

        if (!$booking) {
            return;
        }

        $totalDenda = $booking->penalties()->sum('amount');
        $totalPaid = $this->payments()->sum('pembayaran');

        $this->total_denda = $totalDenda;
        $this->total_paid = $totalPaid;

        $this->total_tagihan =
            $this->base_amount +
            $this->pickup_dropOff +
            $totalDenda;

        $this->sisa_pembayaran =
            $this->total_tagihan - $totalPaid;

        $this->status = $this->sisa_pembayaran <= 0
            ? 'lunas'
            : 'belum_lunas';

        $this->saveQuietly();
    }

}

