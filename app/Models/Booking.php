<?php

namespace App\Models;

use App\Observers\ActivityObserver;
use Illuminate\Database\Eloquent\Model;

class Booking extends Model
{
    protected $fillable = [
        'car_id',
        'customer_id',
        'driver_id',
        'paket',
        'source',
        'tanggal_keluar',
        'tanggal_kembali',
        'waktu_keluar',
        'total_hari',
        'waktu_kembali',
        'harga_harian',
        'estimasi_biaya',
        'identity_file',
        'status',
        'lokasi_pengantaran',
        'lokasi_pengembalian',
        'driver_pengantaran_id',
        'driver_pengembalian_id',
        'ttd',
    ];
    public function driverPengantaran()
    {
        return $this->belongsTo(Driver::class, 'driver_pengantaran_id');
    }

    public function driverPengembalian()
    {
        return $this->belongsTo(Driver::class, 'driver_pengembalian_id');
    }

    public function car()
    {
        return $this->belongsTo(Car::class);
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function driver()
    {
        return $this->belongsTo(Driver::class);
    }
    public function invoice()
    {
        return $this->hasOne(Invoice::class);
    }

    public function penalties()
    {
        return $this->hasMany(Penalty::class);
    }
    protected function handleRecordCreation(array $data): Model
    {
        $record = static::getModel()::create($data);

        // Ubah status booking menjadi 'selesai'
        if (isset($data['booking_id'])) {
            Booking::where('id', $data['booking_id'])
                ->update(['status' => 'selesai']);
        }

        return $record;
    }

    protected $with = [
        'invoice',
    ];
    protected static function booted()
    {
        static::updated(function (Booking $booking) {

            // Jika belum ada invoice → stop
            if (!$booking->invoice) {
                return;
            }

            $invoice = $booking->invoice;

            // Update nilai invoice dari booking
            $invoice->update([
                'estimasi_biaya' => $booking->estimasi_biaya,

                // total_tagihan = sewa + pickup + denda
                'total_tagihan' =>
                    $booking->estimasi_biaya
                    + ($invoice->pickup_dropOff ?? 0)
                    + ($invoice->total_denda ?? 0),

                // sisa = total - sudah dibayar
                'sisa_pembayaran' =>
                    (
                        $booking->estimasi_biaya
                        + ($invoice->pickup_dropOff ?? 0)
                        + ($invoice->total_denda ?? 0)
                    )
                    - ($invoice->total_paid ?? 0),
            ]);
        });
    }


}
