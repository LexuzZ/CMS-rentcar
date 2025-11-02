<?php

namespace App\Filament\Pages\Analytic\Widgets;

use App\Models\Payment;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\DB;

class MetodePembayaranChart extends ChartWidget
{
    protected static ?string $heading = 'Metode Pembayaran';
    protected static ?int $sort = 3;
    public ?string $filter = 'this_month';

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

        // Query sesuai dengan tanggal_pembayaran
        $data = Payment::query()
            ->select('metode_pembayaran', DB::raw('SUM(pembayaran) as total'))
            ->whereBetween('tanggal_pembayaran', [$startDate, $endDate])
            ->where('status', 'lunas')
            ->groupBy('metode_pembayaran')
            ->orderByDesc('total')
            ->get();

        // Warna per metode (disesuaikan dengan enum)
        $colors = [
            'tunai'    => '#2ecc71', // hijau
            'transfer' => '#3498db', // biru
            'qris'     => '#f1c40f', // kuning
        ];

        // Siapkan label dan data
        $labels = ['Tunai', 'Transfer', 'QRIS'];
        $values = [];

        foreach (['tunai', 'transfer', 'qris'] as $method) {
            $values[] = $data->firstWhere('metode_pembayaran', $method)->total ?? 0;
        }

        return [
            'datasets' => [
                [
                    'label' => 'Jumlah Transaksi',
                    'data' => $values,
                    'backgroundColor' => array_values($colors),
                ],
            ],
            'labels' => $labels,
        ];
    }

    protected function getType(): string
    {
        return 'doughnut';
    }
}
