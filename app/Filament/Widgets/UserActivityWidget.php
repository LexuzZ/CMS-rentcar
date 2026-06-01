<?php

namespace App\Filament\Widgets;

use Filament\Widgets\Widget;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use Spatie\Activitylog\Models\Activity;

class UserActivityWidget extends Widget
{
    protected static string $view = 'filament.widgets.user-activity-widget';

    protected static ?int $sort = 5;

    protected int | string | array $columnSpan = 'full';

    // Auto-refresh setiap 60 detik
    protected static ?string $pollingInterval = '60s';

    public function getRecentLogins(): \Illuminate\Support\Collection
    {
        return User::query()
            ->whereNotNull('last_login_at')
            ->orderByDesc('last_login_at')
            ->limit(8)
            ->get(['id', 'name', 'email', 'last_login_at', 'last_login_ip']);
    }

    public function getRecentActivities(): \Illuminate\Support\Collection
    {
        return Activity::query()
            ->with('causer')
            ->latest()
            ->limit(8)
            ->get();
    }

    protected function getViewData(): array
    {
        return [
            'recentLogins'     => $this->getRecentLogins(),
            'recentActivities' => $this->getRecentActivities(),
        ];
    }
}
