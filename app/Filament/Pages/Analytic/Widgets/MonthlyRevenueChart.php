<?php

namespace App\Filament\Pages\Analytic\Widgets;

use App\Models\Payment;
use App\Models\Pengeluaran;
use Filament\Widgets\ChartWidget;
use Filament\Widgets\Concerns\InteractsWithPageFilters;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class MonthlyRevenueChart extends ChartWidget
{
    use InteractsWithPageFilters;

    protected static ?string $heading = 'Pendapatan & Pengeluaran Bulanan';
    protected static ?int $sort = 2;
    protected int|string|array $columnSpan = 'full';

    public ?string $filter = null;

    protected function getFilters(): ?array
    {
        $year = now()->year;

        if (is_null($this->filter)) {
            $this->filter = (string) $year;
        }

        return [
            (string) $year => 'Tahun Ini',
            (string) ($year - 1) => 'Tahun Lalu',
        ];
    }

    protected function getData(): array
    {
        $year = (int) $this->filter;

        /**
         * ===============================
         * LABEL BULAN
         * ===============================
         */
        $months = collect(range(1, 12))
            ->map(fn ($m) => Carbon::create()->month($m)->locale('id')->translatedFormat('F'))
            ->toArray();

        /**
         * ===============================
         * PENDAPATAN (INVOICE LUNAS)
         * ===============================
         */
        $pendapatanRaw = Payment::select(
                DB::raw('MONTH(tanggal_pembayaran) as bulan'),
                DB::raw('SUM(pembayaran) as total')
            )
            ->whereYear('tanggal_pembayaran', $year)
            ->whereHas('invoice', fn ($q) => $q->where('status', 'lunas'))
            ->groupBy(DB::raw('MONTH(tanggal_pembayaran)'))
            ->pluck('total', 'bulan');

        $pendapatan = collect(range(1, 12))
            ->map(fn ($m) => (int) ($pendapatanRaw[$m] ?? 0))
            ->toArray();

        /**
         * ===============================
         * PENGELUARAN
         * ===============================
         */
        $pengeluaranRaw = Pengeluaran::select(
                DB::raw('MONTH(tanggal_pengeluaran) as bulan'),
                DB::raw('SUM(pembayaran) as total')
            )
            ->whereYear('tanggal_pengeluaran', $year)
            ->groupBy(DB::raw('MONTH(tanggal_pengeluaran)'))
            ->pluck('total', 'bulan');

        $pengeluaran = collect(range(1, 12))
            ->map(fn ($m) => (int) ($pengeluaranRaw[$m] ?? 0))
            ->toArray();

        return [
            'datasets' => [
                [
                    'label' => 'Pendapatan',
                    'data' => $pendapatan,
                    'backgroundColor' => '#10B981',
                    'borderColor' => '#10B981',
                ],
                [
                    'label' => 'Pengeluaran',
                    'data' => $pengeluaran,
                    'backgroundColor' => '#F43F5E',
                    'borderColor' => '#F43F5E',
                ],
            ],
            'labels' => $months,
        ];
    }

    protected function getType(): string
    {
        return 'bar';
    }
}
