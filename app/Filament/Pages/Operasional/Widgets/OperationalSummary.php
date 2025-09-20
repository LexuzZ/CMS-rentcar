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

    public function getData(): array
    {
        $start = now()->startOfMonth();
        $end   = now()->endOfMonth();

        // Total sewa bulan ini
        $totalSewa = Booking::whereBetween('tanggal_keluar', [$start, $end])->count();

        // Pendapatan kotor (estimasi biaya + pickup_dropOff)
        $pendapatan = Booking::whereBetween('tanggal_keluar', [$start, $end])
            ->with('invoice')
            ->get()
            ->sum(fn($b) => $b->estimasi_biaya + ($b->invoice?->pickup_dropOff ?? 0));

        // Total denda
        $totalDenda = Booking::whereBetween('tanggal_keluar', [$start, $end])
            ->with('penalty')
            ->get()
            ->sum(fn($b) => $b->penalty->sum('amount'));

        // Keuntungan (contoh sederhana)
        $keuntungan = $pendapatan + $totalDenda;

        // Top 5 mobil terlaris
        $topCars = Booking::select('car_id', DB::raw('COUNT(*) as total'))
            ->whereBetween('tanggal_keluar', [$start, $end])
            ->groupBy('car_id')
            ->with('car.carModel')
            ->orderByDesc('total')
            ->take(5)
            ->get();

        // Top 5 jarang disewa
        $lowCars = Booking::select('car_id', DB::raw('COUNT(*) as total'))
            ->whereBetween('tanggal_keluar', [$start, $end])
            ->groupBy('car_id')
            ->with('car.carModel')
            ->orderBy('total')
            ->take(5)
            ->get();

        return [
            'totalSewa'   => $totalSewa,
            'pendapatan'  => $pendapatan,
            'denda'       => $totalDenda,
            'keuntungan'  => $keuntungan,
            'topCars'     => $topCars,
            'lowCars'     => $lowCars,
        ];
    }

}
