<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserActivity extends Model
{
   public $timestamps = false; // karena hanya pakai created_at
    protected $fillable = ['user_id', 'action', 'module', 'description'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
