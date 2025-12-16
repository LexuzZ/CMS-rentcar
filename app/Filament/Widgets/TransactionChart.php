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
    // protected static ?int $pollingInterval = null;

    protected int|string|array $columnSpan = [
        'sm' => 'full',
        'md' => '6',
        'lg' => '6',
    ];

    protected function getData(): array
    {
        $cacheKey = 'transaction_chart_' . now()->format('Y_m');

        $data = Cache::remember($cacheKey, 120, function () {
            return Payment::query()
                ->selectRaw('DAY(created_at) as day, SUM(pembayaran) as total')
                ->whereYear('created_at', now()->year)
                ->whereMonth('created_at', now()->month)
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
                ],
            ],
            'labels' => $data->pluck('day'),
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }
}
