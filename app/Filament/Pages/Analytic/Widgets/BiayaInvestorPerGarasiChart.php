<?php

namespace App\Filament\Pages\Analytic\Widgets;

use App\Models\Payment;
use App\Models\Pengeluaran;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\ChartWidget;
use Filament\Widgets\TableWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class BiayaInvestorPerGarasiChart extends ChartWidget
{
    protected static ?string $heading = 'Total Biaya Investor per Garasi (Bulan Ini)';
    protected static ?int $sort = 5;
    //  protected int|string|array $columnSpan = 'full';

    public ?string $filter = 'this_month';

    // 2. Opsi filter di dropdown
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
        // 3. Logika untuk menentukan rentang tanggal dinamis
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

        $data = Payment::query()
            ->where('payments.status', 'lunas')
            // 4. Gunakan tanggal dinamis di query
            ->whereBetween('payments.tanggal_pembayaran', [$startDate, $endDate])
            ->join('invoices', 'payments.invoice_id', '=', 'invoices.id')
            ->join('bookings', 'invoices.booking_id', '=', 'bookings.id')
            ->join('cars', 'bookings.car_id', '=', 'cars.id')
            ->where('cars.garasi', '!=', 'SPT')
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
                    'backgroundColor' => ['#3498db', '#2ecc71', '#9b59b6', '#f1c40f', '#e74c3c'],
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
