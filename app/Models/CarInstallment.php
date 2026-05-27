<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CarInstallment extends Model
{
    protected $fillable = [
        'car_id',
        'nama_leasing',
        'tanggal_mulai',
        'jatuh_tempo',
        'total_hutang',
        'nominal_cicilan',
        'tenor',
        'cicilan_ke',
        'total_dibayar',
        'sisa_hutang',
        'status',
        'catatan',
    ];

    protected static function booted()
    {
        static::saving(function ($record) {

            $record->total_dibayar =
                $record->nominal_cicilan * $record->cicilan_ke;

            $record->sisa_hutang =
                $record->total_hutang - $record->total_dibayar;

            if ($record->sisa_hutang <= 0) {
                $record->status = 'lunas';
                $record->sisa_hutang = 0;
            }
        });
    }

    public function car()
    {
        return $this->belongsTo(Car::class);
    }
}
