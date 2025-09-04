<?php

namespace App\Filament\Pages\Analytic\Widgets;

use App\Models\Invoice;
use App\Models\Payment;
use App\Models\Penalty;
use App\Models\Pengeluaran;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Carbon;
use Filament\Forms\Components\Select;
use Filament\Actions\Action;
use Filament\Tables\Actions\Action as ActionsAction;

class MonthlyRevenueChart extends ChartWidget
{
    protected static ?string $heading = 'Pendapatan & Pengeluaran Bulanan';

    protected int|string|array $columnSpan = 'full';

    protected function getData(): array
    {
        $year = Carbon::now()->year;

        // Siapkan array 12 bulan
        $months = collect(range(1, 12))->map(function ($m) {
            return Carbon::create()->month($m)->locale('id')->translatedFormat('F');
        })->toArray();

        // Hitung total pendapatan per bulan
        $pendapatan = collect(range(1, 12))->map(function ($month) use ($year) {
            return Payment::whereYear('tanggal_pembayaran', $year)
                ->where('status', 'lunas')
                ->whereMonth('tanggal_pembayaran', $month)
                ->sum('pembayaran'); // sesuaikan field total/jumlah
        })->toArray();

        // Hitung total pengeluaran per bulan
        $pengeluaran = collect(range(1, 12))->map(function ($month) use ($year) {
            return Pengeluaran::whereYear('tanggal_pengeluaran', $year)
                ->whereMonth('tanggal_pengeluaran', $month)
                ->sum('pembayaran'); // sesuaikan field jumlah
        })->toArray();

        return [
            'datasets' => [
                [
                    'label' => 'Pendapatan',
                    'data' => $pendapatan,
                    'backgroundColor' => 'rgba(34,197,94,0.7)', // hijau
                    'borderColor' => 'rgba(34,197,94,1)',
                ],
                [
                    'label' => 'Pengeluaran',
                    'data' => $pengeluaran,
                    'backgroundColor' => 'rgba(239,68,68,0.7)', // merah
                    'borderColor' => 'rgba(239,68,68,1)',
                ],
            ],
            'labels' => $months,
        ];
    }

    protected function getType(): string
    {
        return 'bar'; // bisa diganti 'line'
    }
    public static function canViewAny(): bool
    {
        // Hanya pengguna dengan peran 'admin' yang bisa melihat resource ini
        return auth()->user()->isAdmin();
    }
}
