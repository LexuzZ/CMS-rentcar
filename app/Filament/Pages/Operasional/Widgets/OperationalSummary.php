<?php

namespace App\Filament\Pages\Operasional\Widgets;

use App\Models\Car;
use App\Models\Booking;
use App\Models\Penalty;
use Carbon\Carbon;
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

    public function getData(): array
    {
        $start = Carbon::now()->startOfMonth();
        $end = Carbon::now()->endOfMonth();

        $totalSewa = Booking::whereBetween('tanggal_keluar', [$start, $end])->count();
        $pendapatan = Booking::whereBetween('tanggal_keluar', [$start, $end])->sum('estimasi_biaya');
        $denda = Penalty::whereBetween('created_at', [$start, $end])->sum('amount');
        $keuntungan = $pendapatan + $denda;

        $topCars = Booking::selectRaw('car_id, count(*) as total')
            ->whereBetween('tanggal_keluar', [$start, $end])
            ->groupBy('car_id')
            ->with('car.carModel')
            ->orderByDesc('total')
            ->take(5)
            ->get();

        $lowCars = Booking::selectRaw('car_id, count(*) as total')
            ->whereBetween('tanggal_keluar', [$start, $end])
            ->groupBy('car_id')
            ->with('car.carModel')
            ->orderBy('total')
            ->take(5)
            ->get();

        return compact('totalSewa', 'pendapatan', 'denda', 'keuntungan', 'topCars', 'lowCars');
    }

}
