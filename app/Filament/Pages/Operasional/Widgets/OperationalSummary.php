<?php

namespace App\Filament\Pages\Operasional\Widgets;

use App\Models\Car;
use App\Models\Booking;
use Filament\Widgets\Widget;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\DB;

class OperationalSummary extends Widget
{
    protected static string $view = 'filament.widgets.operational-summary';

    public static function canView(): bool
    {
        return auth()->user()->hasAnyRole(['superadmin', 'admin']);
    }

    public function getTopCars()
    {
        $startOfMonth = now()->startOfMonth();
        $endOfMonth = now()->endOfMonth();

        $counts = Booking::select('car_id', DB::raw('COUNT(*) as total'))
            ->whereBetween('tanggal_keluar', [$startOfMonth, $endOfMonth])
            ->groupBy('car_id')
            ->with('car.carModel')
            ->get();

        $top5 = $counts->sortByDesc('total')->take(5);
        $low5 = $counts->sortBy('total')->take(5);

        return [
            'top5' => $top5,
            'low5' => $low5,
        ];
    }

}
