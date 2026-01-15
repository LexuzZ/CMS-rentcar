<?php

namespace App\Filament\Widgets;

use App\Models\Payment;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\Cache;

class TransactionChart extends ChartWidget
{
    protected static ?string $heading = 'Transaksi Bulan Ini';
    protected static ?int $sort = 3;

    protected static bool $isLazy = true;

    protected int|string|array $columnSpan = [
        'sm' => 'full',
        'md' => 6,
        'lg' => 6,
    ];

    protected function getData(): array
    {
        $year  = now()->year;
        $month = now()->month;

        $cacheKey = "transaction_chart_{$year}_{$month}";

        $data = Cache::remember($cacheKey, 300, function () use ($year, $month) {
            return Payment::query()
                ->selectRaw('
                    DAY(tanggal_pembayaran) as day,
                    SUM(pembayaran) as total
                ')
                ->whereYear('tanggal_pembayaran', $year)
                ->whereMonth('tanggal_pembayaran', $month)
                ->groupBy('day')
                ->orderBy('day')
                ->get();
        });

        return [
            'datasets' => [
                [
                    'label' => 'Total Pembayaran',
                    'data' => $data->pluck('total')->map(fn ($v) => (int) $v),
                    'fill' => true,
                    'tension' => 0.3,
                ],
            ],
            'labels' => $data->pluck('day')->map(fn ($d) =>  $d),
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }
}
