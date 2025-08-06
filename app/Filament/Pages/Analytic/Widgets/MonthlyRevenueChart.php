<?php

namespace App\Filament\Pages\Analytic\Widgets;

use App\Models\Invoice;
use App\Models\Penalty;
use App\Models\Pengeluaran;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Carbon;
use Filament\Forms\Components\Select;
use Filament\Actions\Action;
use Filament\Tables\Actions\Action as ActionsAction;

class MonthlyRevenueChart extends ChartWidget
{
    protected static ?string $heading = 'Pendapatan vs Pengeluaran Bulanan';
    protected static ?int $sort = 3;
    protected static ?string $maxHeight = '300px';
    protected int | string | array $columnSpan = 'full';

    protected function getType(): string
    {
        return 'bar';
    }

    protected function getFormSchema(): array
    {
        $currentYear = now()->year;
        $years = Invoice::selectRaw('YEAR(tanggal_invoice) as year')
            ->distinct()
            ->orderByDesc('year')
            ->pluck('year', 'year')
            ->toArray();

        if (empty($years)) {
            $years = [$currentYear => $currentYear];
        }

        return [
            Select::make('year')
                ->label('Tahun')
                ->options($years)
                ->default($currentYear)
                ->reactive(),
        ];
    }

    protected function getData(): array
    {
        $selectedYear = $this->filterFormData['year'] ?? now()->year;

        // Pendapatan dari Invoice
        $invoiceRevenue = Invoice::selectRaw('MONTH(tanggal_invoice) as month, SUM(total) as total')
            ->whereYear('tanggal_invoice', $selectedYear)
            ->groupBy('month')
            ->pluck('total', 'month');

        // Pendapatan dari Denda
        $penaltyRevenue = Penalty::selectRaw('MONTH(created_at) as month, SUM(amount) as total')
            ->whereYear('created_at', $selectedYear)
            ->groupBy('month')
            ->pluck('total', 'month');

        // Pengeluaran per bulan
        $monthlyExpenses = Pengeluaran::selectRaw('MONTH(tanggal_pengeluaran) as month, SUM(pembayaran) as total')
            ->whereYear('tanggal_pengeluaran', $selectedYear)
            ->groupBy('month')
            ->pluck('total', 'month');

        $labels = collect(range(1, 12))->map(fn($m) => \Carbon\Carbon::create()->month($m)->format('M'));

        $pendapatan = collect(range(1, 12))->map(function ($m) use ($invoiceRevenue, $penaltyRevenue) {
            return $invoiceRevenue->get($m, 0) + $penaltyRevenue->get($m, 0);
        });

        $pengeluaran = collect(range(1, 12))->map(function ($m) use ($monthlyExpenses) {
            return $monthlyExpenses->get($m, 0);
        });

        return [
            'datasets' => [
                [
                    'label' => "Total Pendapatan (Sewa + Denda)",
                    'data' => $pendapatan,
                    'backgroundColor' => '#347433', // hijau
                    'borderRadius' => 6,
                ],
                [
                    'label' => "Total Pengeluaran",
                    'data' => $pengeluaran,
                    'backgroundColor' => '#B22222', // merah
                    'borderRadius' => 4,
                ],
            ],
            'labels' => $labels,
        ];
    }
}
