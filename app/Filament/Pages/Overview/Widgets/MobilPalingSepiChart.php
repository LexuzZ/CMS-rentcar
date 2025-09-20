<?php

namespace App\Filament\Pages\Overview\Widgets;

use App\Models\Booking;
use Carbon\Carbon;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\DB;

class MobilPalingSepiChart extends ChartWidget
{
    protected static ?string $heading = 'Top 5 Mobil Paling Sepi Peminat';
    protected static ?int $sort = 3;

    protected function getData(): array
    {
        $startOfMonth = Carbon::now()->startOfMonth();
        $endOfMonth = Carbon::now()->endOfMonth();

        $data = Booking::query()
            ->whereBetween('tanggal_keluar', [$startOfMonth, $endOfMonth])
            ->join('cars', 'bookings.car_id', '=', 'cars.id')
            ->join('car_models', 'cars.car_model_id', '=', 'car_models.id')
            ->select('car_models.name as model_name', DB::raw('count(*) as total'))
            ->groupBy('car_models.name')
            ->orderBy('total', 'asc') // Urutkan dari yang terkecil
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
            'labels' => $data->pluck('model_name')->toArray(),
        ];

        // return [
        //     'datasets' => [
        //         [
        //             'label' => 'Jumlah Penyewaan',
        //             'data' => $data->pluck('total')->toArray(),
        //         ],
        //     ],
        //     'labels' => $data->pluck('model_name')->toArray(),
        // ];
    }

    protected function getType(): string
    {
        return 'horizontalBar'; // Tipe grafik batang horizontal
    }
}
