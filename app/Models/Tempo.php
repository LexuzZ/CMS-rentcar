<?php

namespace App\Models;

use App\Observers\ActivityObserver;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class Tempo extends Model
{
    //
    use HasFactory;
    protected $fillable = [
        'car_id',
        'perawatan',
        'jatuh_tempo',
        'description',
    ];
    public function car()
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
