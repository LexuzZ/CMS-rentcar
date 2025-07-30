<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Car extends Model
{
    use HasFactory;
     protected $fillable = [
        'nopol',
        'merek',
        'nama_mobil',
        'warna',
        'transmisi',
        'garasi',
        'year',
        'status',
        'harga_pokok',
        'harga_harian',
        'harga_bulanan',
        'photo',
    ];
    public function bookings()
{
    return $this->hasMany(Booking::class);
}
}
