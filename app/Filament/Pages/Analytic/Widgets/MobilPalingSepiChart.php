<?php

namespace App\Filament\Pages\Analytic\Widgets;

use App\Models\Booking;
use Carbon\Carbon;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\DB;

class MobilPalingSepiChart extends ChartWidget
{
    protected static ?string $heading = 'Top 5 Mobil Paling Sepi Peminat (per Unit)'; // Judul diubah
    protected static ?int $sort = 3;

    public ?string $filter = 'this_month';

    // 2. Tambahkan metode untuk mendefinisikan opsi filter
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
        // 3. Tentukan rentang tanggal secara dinamis berdasarkan filter yang aktif
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

        // 4. Query menggunakan rentang tanggal dinamis tersebut
        $data = Booking::query()
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
            ->orderBy('total', 'asc') // <-- Tetap 'asc' untuk mencari yang paling sepi
            ->limit(5)
            ->get();

        return [
            'datasets' => [
                [
                    'label' => 'Total Hari Disewa',
                    'data' => $data->pluck('total')->toArray(),
                    'backgroundColor' => ['#E74C3C', '#F1C40F', '#9B59B6', '#2ECC71', '#3498DB'],
                ],
            ],
            'labels' => $data->map(fn ($item) => "{$item->model_name} ({$item->nopol})")->toArray(),
        ];
    }

    protected function getType(): string
    {
        return 'bar';
    }
}
