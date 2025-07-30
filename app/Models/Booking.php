<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Booking extends Model
{
    protected $fillable = [
        'car_id',
        'customer_id',
        'driver_id',
        'paket',
        'tanggal_keluar',
        'tanggal_kembali',
        'waktu_keluar',
        'total_hari',
        'waktu_kembali',
        'harga_harian',
        'estimasi_biaya',
        'identity_file',
        'status',
    ];

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

    public function penalty()
    {
        return $this->hasMany(Penalty::class);
    }
}
