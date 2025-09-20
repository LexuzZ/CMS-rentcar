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
        // Query untuk menghitung total biaya investor per garasi
        $data = Payment::query()
            ->where('status', 'lunas')
            ->whereBetween('tanggal_pembayaran', [now()->startOfMonth(), now()->endOfMonth()])
            ->join('invoices', 'payments.invoice_id', '=', 'invoices.id')
            ->join('bookings', 'invoices.booking_id', '=', 'bookings.id')
            ->join('cars', 'bookings.car_id', '=', 'cars.id') // Join ke tabel mobil
            ->select(
                'cars.garasi as nama_garasi', // <-- Ambil nama garasi dari tabel 'cars'
                // Hitung total biaya pokok (investor)
                DB::raw('SUM(cars.harga_investor * bookings.total_hari) as total_biaya_investor')
            )
            ->groupBy('cars.garasi') // <-- Kelompokkan berdasarkan kolom 'garasi'
            ->orderByDesc('total_biaya_investor')
            ->get();

        // Jika tidak ada data, kembalikan array kosong
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
