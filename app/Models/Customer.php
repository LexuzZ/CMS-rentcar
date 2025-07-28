<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Testing\Fluent\Concerns\Has;

class Customer extends Model
{
    use HasFactory;
    protected $fillable = [
        'nama',
        'ktp',
        'no_telp',
        'alamat',
    ];
}
