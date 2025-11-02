<?php

namespace App\Filament\Pages\Analytic\Widgets;

use App\Models\Payment;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\DB;

class MetodePembayaranChart extends ChartWidget
{
    protected static ?string $heading = 'Metode Pembayaran Terpopuler';
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

        // Ambil data dari tabel payments berdasarkan tanggal_pembayaran
        $data = Payment::query()
            ->select('metode', DB::raw('COUNT(*) as total'))
            ->whereBetween('tanggal_pembayaran', [$startDate, $endDate])
            ->groupBy('metode_pembayaran')
            ->orderByDesc('total')
            ->get();

        // Warna-warna yang rapi
        $colors = [
            'tunai' => '#2ecc71',     // Hijau
            'transfer' => '#3498db',  // Biru
            'qris' => '#f1c40f',      // Kuning
            'lainnya' => '#95a5a6',   // Abu
        ];

        // Map data ke label dan warna
        $labels = [];
        $values = [];
        $bgColors = [];

        foreach ($data as $row) {
            $method = $row->metode ?? 'lainnya';
            $labels[] = ucfirst($method);
            $values[] = $row->total;
            $bgColors[] = $colors[$method] ?? '#7f8c8d';
        }

        return [
            'datasets' => [
                [
                    'label' => 'Jumlah Pembayaran',
                    'data' => $values,
                    'backgroundColor' => $bgColors,
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
