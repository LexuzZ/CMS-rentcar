<?php

namespace App\Filament\Widgets;

use App\Models\LoginActivity;
use Filament\Widgets\Widget;
use Illuminate\Support\Facades\Auth;

class LoginActivityWidget extends Widget
{
    protected static string $view = 'filament.widgets.login-activity-widget';
    protected static ?int   $sort = 6;
    protected static bool   $isLazy = true;
    // protected int|string|array $columnSpan = [
    //     'sm' => 'full',
    //     'md' => '10',
    //     'lg' => '10',
    // ];
    protected int|string|array $columnSpan = 'half';

    protected static ?string $pollingInterval = '120s';

    public int $perPage = 10;
    public string $filterStatus = 'all';   // all | success | failed
    public string $filterPeriod = 'today'; // today | week | month

    public function loadMore(): void
    {
        $this->perPage += 10;
    }

    public function setFilter(string $status): void
    {
        $this->filterStatus = $status;
        $this->perPage = 10;
    }

    public function setPeriod(string $period): void
    {
        $this->filterPeriod = $period;
        $this->perPage = 10;
    }

    protected function getViewData(): array
    {
        $query = LoginActivity::with('user')
            ->orderByDesc('logged_in_at');

        // Filter periode
        $query->when($this->filterPeriod === 'today', fn($q) =>
            $q->whereDate('logged_in_at', today())
        )->when($this->filterPeriod === 'week', fn($q) =>
            $q->where('logged_in_at', '>=', now()->subWeek())
        )->when($this->filterPeriod === 'month', fn($q) =>
            $q->where('logged_in_at', '>=', now()->startOfMonth())
        );

        // Filter status
        $query->when($this->filterStatus !== 'all', fn($q) =>
            $q->where('status', $this->filterStatus)
        );

        $total      = $query->count();
        $activities = $query->limit($this->perPage)->get();

        // Summary stats
        $todaySuccess = LoginActivity::whereDate('logged_in_at', today())->where('status', 'success')->count();
        $todayFailed  = LoginActivity::whereDate('logged_in_at', today())->where('status', 'failed')->count();
        $uniqueUsers  = LoginActivity::whereDate('logged_in_at', today())->where('status', 'success')
                            ->distinct('user_id')->count();

        return [
            'activities'   => $activities,
            'total'        => $total,
            'hasMore'      => $total > $this->perPage,
            'todaySuccess' => $todaySuccess,
            'todayFailed'  => $todayFailed,
            'uniqueUsers'  => $uniqueUsers,
            'filterStatus' => $this->filterStatus,
            'filterPeriod' => $this->filterPeriod,
        ];
    }

    public static function canView(): bool
    {
        return Auth::user()?->hasAnyRole(['superadmin', 'admin']);
    }
}
