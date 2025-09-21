<?php

namespace App\Filament\Pages\Analytic\Widgets;

use App\Models\Booking;
use Carbon\Carbon;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\DB;

class MobilTerlarisChart extends ChartWidget
{
    // protected ?string $description = 'An overview of some analytics.';


    protected static ?string $heading = 'Top 5 Mobil Paling Laris Disewa (per Unit)'; // Judul diubah
    protected static ?int $sort = 2;
    public ?string $filter = 'this_month';

    // Opsi filter di dropdown (ini sudah benar)
    protected function getFilters(): ?array
    {
        return [
            'this_month' => 'Bulan Ini',
            'last_month' => 'Bulan Lalu',
            'this_year' => 'Tahun Ini',
        ];
    }

    protected function getData(): array
    {
        // 1. Tentukan rentang tanggal secara dinamis berdasarkan filter yang aktif
        $activeFilter = $this->filter;

        $startDate = match ($activeFilter) {
            'this_month' => now()->startOfMonth(),
            'last_month' => now()->subMonth()->startOfMonth(),
            'this_year' => now()->startOfYear(),
        };

        $endDate = match ($activeFilter) {
            'this_month' => now()->endOfMonth(),
            'last_month' => now()->subMonth()->endOfMonth(),
            'this_year' => now()->endOfYear(),
        };

        // 2. Query menggunakan rentang tanggal dinamis tersebut
        $data = Booking::query()
            // Gunakan $startDate dan $endDate di sini
            ->whereBetween('tanggal_keluar', [$startDate, $endDate])
            ->join('cars', 'bookings.car_id', '=', 'cars.id')
            ->where('cars.garasi', 'SPT')
            ->join('car_models', 'cars.car_model_id', '=', 'car_models.id')
            ->select(
                'car_models.name as model_name',
                'cars.nopol',
                DB::raw('SUM(bookings.total_hari) as total')
            )
            ->groupBy('car_models.name', 'cars.nopol')
            ->orderByDesc('total')
            ->limit(5)
            ->get();

        return [
            'datasets' => [
                [
                    'label' => 'Total Hari Disewa',
                    'data' => $data->pluck('total')->toArray(),
                    'backgroundColor' => ['#3498db', '#2ecc71', '#9b59b6', '#f1c40f', '#e74c3c'],
                ],
            ],
            'labels' => $data->map(fn($item) => "{$item->model_name} ({$item->nopol})")->toArray(),
        ];
    }

    protected function getType(): string
    {
        return 'bar';
    }
}
