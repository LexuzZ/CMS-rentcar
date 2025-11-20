<?php

namespace App\Observers;

use App\Models\ActivityLog;
use App\Models\UserActivity;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class ActivityObserver
{
    public function created(Model $model)
    {
        $this->log('create', $model);
    }

    public function updated(Model $model)
    {
        $this->log('update', $model);
    }

    public function deleted(Model $model)
    {
        $this->log('delete', $model);
    }

    protected function log(string $action, Model $model)
    {
        // Pastikan user ada
        $userId = Auth::id();
        if (! $userId) {
            return;
        }

        UserActivity::create([
            'user_id' => $userId,
            'action' => $action,
            'module' => class_basename($model), // misal "Booking" atau "Payment"
            'description' => $this->makeDescription($action, $model),
        ]);
    }

    protected function makeDescription(string $action, Model $model): string
    {
        $class = class_basename($model);
        $id = $model->getKey();

        return match ($action) {
            'create' => "Membuat {$class} #{$id}",
            'update' => "Memperbarui {$class} #{$id}",
            'delete' => "Menghapus {$class} #{$id}",
            default => "{$action} pada {$class} #{$id}",
        };
    }
}
