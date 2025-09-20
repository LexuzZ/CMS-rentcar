<?php

namespace App\Filament\Pages\Overview\Widgets;

use App\Models\Booking;
use Carbon\Carbon;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\DB;

class MobilTerlarisChart extends ChartWidget
{


    protected static ?string $heading = 'Top 5 Mobil Paling Laris Disewa (per Unit)'; // Judul diubah
    protected static ?int $sort = 2;

    protected function getData(): array
    {
        $startOfMonth = Carbon::now()->startOfMonth();
        $endOfMonth = Carbon::now()->endOfMonth();

        $data = Booking::query()
            ->whereBetween('tanggal_keluar', [$startOfMonth, $endOfMonth])
            ->join('cars', 'bookings.car_id', '=', 'cars.id')
            ->where('cars.garasi', 'SPT')
            ->join('car_models', 'cars.car_model_id', '=', 'car_models.id')
            // UBAH: Pilih nopol dan nama model untuk label
            ->select(
                'car_models.name as model_name',
                'cars.nopol',
                DB::raw('count(bookings.tanggal_keluar) as total')
            )
            // UBAH: Kelompokkan berdasarkan nopol (dan nama model)
            ->groupBy('car_models.name', 'cars.nopol')
            ->orderByDesc('total')
            ->limit(5)
            ->get();

        return [
            'datasets' => [
                [
                    'label' => 'Jumlah Penyewaan',
                    'data' => $data->pluck('total')->toArray(),
                    'backgroundColor' => ['#3498db', '#2ecc71', '#9b59b6', '#f1c40f', '#e74c3c'],
                ],
            ],
            // UBAH: Buat label yang lebih deskriptif (Model + Nopol)
            'labels' => $data->map(fn($item) => "{$item->model_name} ({$item->nopol})")->toArray(),
        ];
    }

    protected function getType(): string
    {
        return 'bar';
    }
}
