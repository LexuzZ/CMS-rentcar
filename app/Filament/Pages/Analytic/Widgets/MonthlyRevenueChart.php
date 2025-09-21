<?php

namespace App\Filament\Pages\Analytic\Widgets;

use App\Models\Payment;
use App\Models\Pengeluaran;
use Filament\Forms\Components\Select;
use Filament\Widgets\ChartWidget;
use Filament\Widgets\Concerns\InteractsWithPageFilters;
use Illuminate\Support\Carbon;

class MonthlyRevenueChart extends ChartWidget
{
    use InteractsWithPageFilters;

    protected static ?string $heading = 'Pendapatan & Pengeluaran Bulanan';
    protected static ?int $sort = 2;
    protected int|string|array $columnSpan = 'full';

    public ?string $filter = null;

    /**
     * Menerapkan filter dasar untuk memilih tahun.
     */
    protected function getFilters(): ?array
    {
        $currentYear = now()->year;
        $lastYear = now()->subYear()->year;

        // Atur filter default ke tahun ini jika belum ada yang dipilih
        if (is_null($this->filter)) {
            $this->filter = $currentYear;
        }

        return [
            $currentYear => 'Tahun Ini',
            $lastYear => 'Tahun Lalu',
        ];
    }

    protected function getData(): array
    {
        // Gunakan nilai dari properti filter publik
        $year = (int) $this->filter;

        // Siapkan array 12 bulan untuk label chart
        $months = collect(range(1, 12))->map(function ($m) {
            return Carbon::create()->month($m)->locale('id')->translatedFormat('F');
        })->toArray();

        // Hitung total pendapatan per bulan untuk tahun yang difilter
        $pendapatan = collect(range(1, 12))->map(function ($month) use ($year) {
            return Payment::whereYear('tanggal_pembayaran', $year)
                ->where('status', 'lunas')
                ->whereMonth('tanggal_pembayaran', $month)
                ->sum('pembayaran');
        })->toArray();

        // Hitung total pengeluaran per bulan untuk tahun yang difilter
        $pengeluaran = collect(range(1, 12))->map(function ($month) use ($year) {
            return Pengeluaran::whereYear('tanggal_pengeluaran', $year)
                ->whereMonth('tanggal_pengeluaran', $month)
                ->sum('pembayaran');
        })->toArray();

        return [
            'datasets' => [
                [
                    'label' => 'Pendapatan',
                    'data' => $pendapatan,
                    'backgroundColor' => '#10B981', // Warna ungu
                    'borderColor' => '#10B981',
                ],
                [
                    'label' => 'Pengeluaran',
                    'data' => $pengeluaran,
                    'backgroundColor' => '#F43F5E', // Warna ungu muda
                    'borderColor' => '#F43F5E',
                ],
            ],
            'labels' => $months,
        ];
    }

    protected function getType(): string
    {
        return 'bar'; // atau 'line'
    }
}

