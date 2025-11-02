<?php

namespace App\Filament\Pages\Analytic\Widgets;

use App\Models\Booking;
use App\Models\Payment;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\DB;

class SumberOrderanChart extends ChartWidget
{
    protected static ?string $heading = 'Sumber Orderan';
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
        $data = Booking::query()
            ->select('source', DB::raw('COUNT(*) as total'))
            ->whereBetween('tanggal_keluar', [$startDate, $endDate])
            // ->where('status', 'lunas')
            ->groupBy('source')
            ->orderByDesc('total')
            ->get();

        // Warna per metode (disesuaikan dengan enum)
        $colors = [
            // 'website','agent','cust_garasi','tiktok','traveloka','ro','tiket','instagram','facebook'
            'website'    => '#2ecc71', // hijau
            'agent' => '#3498db', // biru
            'cust_garasi'     => '#8B5CF6', // kuning
            'tiktok'     => '#000', // kuning
            'traveloka'     => '#F43F5E', // kuning
            'ro'     => '#10B981', // kuning
            'tiket'     => '#f1c40f', // kuning
            'instagram'     => '#F59E0B', // kuning
            'facebook'     => '#38BDF8', // kuning
        ];

        // Siapkan label dan data
        $labels = ['Website','Agent','Cust. Garasi','Tiktok','Traveloka','Repeat Order','Tiket','Instagram','Facebook'];
        $values = [];

        foreach (['website','agent','cust_garasi','tiktok','traveloka','ro','tiket','instagram','facebook'] as $method) {
            $values[] = $data->firstWhere('source', $method)->total ?? 0;
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
