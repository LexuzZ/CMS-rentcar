<?php

namespace App\Models;

use App\Observers\ActivityObserver;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class Payment extends Model
{
    protected $fillable = [
        'invoice_id',
        'pembayaran',
        'metode_pembayaran',
        'tanggal_pembayaran',
        'proof',
        'status',
    ];

    public function invoice()
    {
        return $this->belongsTo(Invoice::class);
    }
    use LogsActivity;
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logAll()
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs()
            ->setDescriptionForEvent(fn(string $eventName) => $eventName);
    }

}
