<?php

namespace App\Filament\Pages\Overview\Widgets;

use App\Models\Payment;
use App\Models\Pengeluaran;
use Filament\Widgets\ChartWidget;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class BiayaInvestorPerGarasiChart extends ChartWidget
{
    protected static ?string $heading = 'Total Biaya Investor per Garasi (Bulan Ini)';
    protected static ?int $sort = 5;

    protected function getData(): array
    {
        $data = Payment::query()
            // PERBAIKAN DI SINI: Sebutkan nama tabelnya secara eksplisit
            ->where('payments.status', 'lunas')
            ->whereBetween('payments.tanggal_pembayaran', [now()->startOfMonth(), now()->endOfMonth()])
            ->join('invoices', 'payments.invoice_id', '=', 'invoices.id')
            ->join('bookings', 'invoices.booking_id', '=', 'bookings.id')
            ->join('cars', 'bookings.car_id', '=', 'cars.id')
            ->select(
                'cars.garasi as nama_garasi',
                DB::raw('SUM(cars.harga_pokok * bookings.total_hari) as total_biaya_investor')
            )
            ->groupBy('cars.garasi')
            ->orderByDesc('total_biaya_investor')
            ->get();

        if ($data->isEmpty()) {
            return [
                'datasets' => [],
                'labels' => [],
            ];
        }

        return [
            'datasets' => [
                [
                    'label' => 'Total Biaya Investor',
                    'data' => $data->pluck('total_biaya_investor')->toArray(),
                    'backgroundColor' => '#4CAF50',
                    'borderColor' => '#4CAF50',
                ],
            ],
            'labels' => $data->pluck('nama_garasi')->toArray(),
        ];
    }

    protected function getType(): string
    {
        return 'bar';
    }
}
