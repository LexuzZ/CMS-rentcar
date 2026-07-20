<?php

namespace App\Filament\Pages\Worksheet\Widgets;

use App\Models\Attendance;
use App\Models\User;
use Filament\Widgets\Widget;
use Illuminate\Support\Facades\Auth;

class AttendanceTodayWidget extends Widget
{
    protected static string $view = 'filament.widgets.attendance-today-widget';
    protected static ?int $sort = 1;
    protected static bool $isLazy = true;

    public function getViewData(): array
    {
        $attended = Attendance::with('user')
            ->whereDate('date', today())
            ->orderBy('check_in_time')
            ->get();

        $totalStaff     = User::count();
        $totalHadir     = $attended->where('status', 'hadir')->count();
        $totalTerlambat = $attended->where('status', 'terlambat')->count();
        $totalBelum     = max(0, $totalStaff - $attended->count());

        return compact('attended', 'totalStaff', 'totalHadir', 'totalTerlambat', 'totalBelum');
    }

    // public static function canView(): bool
    // {
    //     return Auth::user()->hasAnyRole(['superadmin', 'admin']);
    // }
}
