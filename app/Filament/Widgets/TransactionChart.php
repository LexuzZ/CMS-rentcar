<?php

namespace App\Filament\Widgets;

use App\Models\Payment;
use Filament\Widgets\ChartWidget;

class TransactionChart extends ChartWidget
{
    protected static ?string $heading = 'Transaksi Bulan Ini';
    protected static ?int $sort = 3;
     protected int|string|array $columnSpan = '2/3';

    protected function getData(): array
    {
        $data = Payment::selectRaw('DAY(created_at) as day, SUM(pembayaran) as total')
            ->whereMonth('created_at', now()->month)
            ->groupBy('day')
            ->orderBy('day')
            ->get();

        return [
            'datasets' => [
                [
                    'label' => 'Total Pembayaran',
                    'data' => $data->pluck('total'),
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
