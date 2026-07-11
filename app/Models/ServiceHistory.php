<?php

namespace App\Models;

use App\Observers\ActivityObserver;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class ServiceHistory extends Model
{
    //
     use HasFactory;

    protected $fillable = [
        'car_id',
        'service_date',
        'jenis_service',
        'current_km',
        'description',
        'nota_service',
        'workshop',
        'next_km',
        'next_service_date',
    ];

    public function car(): BelongsTo
    {
        return $this->belongsTo(Car::class);
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
