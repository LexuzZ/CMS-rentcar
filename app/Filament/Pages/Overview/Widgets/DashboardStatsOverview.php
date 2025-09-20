<?php

namespace App\Filament\Pages\Overview\Widgets;

use App\Models\Payment;
use App\Models\Pengeluaran;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\Auth;

class DashboardStatsOverview extends BaseWidget
{
    private function calculatePercentageChange(float $current, float $previous): float
    {
        if ($previous == 0) {
            return $current > 0 ? 100 : 0;
        }
        return (($current - $previous) / $previous) * 100;
    }

    protected function getStats(): array
    {
        // --- RENTANG WAKTU ---
        $startOfMonth = now()->startOfMonth();
        $endOfMonth = now()->endOfMonth();
        $startOfLastMonth = now()->subMonth()->startOfMonth();
        $endOfLastMonth = now()->subMonth()->endOfMonth();

        // --- PEMASUKAN (INCOME) ---
        // Pemasukan Bulan Ini (hanya dari pembayaran yang sudah 'lunas')
        $incomeThisMonth = Payment::whereBetween('tanggal_pembayaran', [$startOfMonth, $endOfMonth])
            ->where('status', 'lunas')
            ->get()
            ->sum(function ($payment) {
                $totalDays = $payment->invoice->booking->total_hari;
                $hargaPokokTotal = $payment->invoice->booking->car->harga_pokok * $totalDays;
                $hargaHarianTotal = $payment->invoice->booking->car->harga_harian * $totalDays;
                return $hargaHarianTotal - $hargaPokokTotal;
            });
        $RevenueMonth = Payment::whereBetween('tanggal_pembayaran', [$startOfMonth, $endOfMonth])
            ->where('status', 'lunas')
            ->sum('pembayaran');
        $revenueThisMonth = Payment::whereBetween('tanggal_pembayaran', [$startOfMonth, $endOfMonth])
            ->where('status', 'lunas')
            ->sum('pembayaran');

        // Piutang Bulan Lalu
        $revenueLastMonth = Payment::whereBetween('tanggal_pembayaran', [$startOfMonth, $endOfMonth])
            ->where('status', 'lunas')
            ->sum('pembayaran');

        $revenueChange = $this->calculatePercentageChange($revenueThisMonth, $revenueLastMonth);

        // Pemasukan Bulan Lalu
        $incomeLastMonth = Payment::whereBetween('tanggal_pembayaran', [$startOfMonth, $endOfMonth])
            ->where('status', 'lunas')
            ->get()
            ->sum(function ($payment) {
                $totalDays = $payment->invoice->booking->total_hari;
                $hargaPokokTotal = $payment->invoice->booking->car->harga_pokok * $totalDays;
                $hargaHarianTotal = $payment->invoice->booking->car->harga_harian * $totalDays;
                return $hargaHarianTotal - $hargaPokokTotal;
            });

        $incomeChange = $this->calculatePercentageChange($incomeThisMonth, $incomeLastMonth);

        // --- PENGELUARAN (EXPENSE) ---
        // Pengeluaran Bulan Ini
        $expenseThisMonth = Pengeluaran::whereBetween('tanggal_pengeluaran', [$startOfMonth, $endOfMonth])
            ->sum('pembayaran');

        // Pengeluaran Bulan Lalu
        $expenseLastMonth = Pengeluaran::whereBetween('tanggal_pengeluaran', [$startOfLastMonth, $endOfLastMonth])
            ->sum('pembayaran');

        $expenseChange = $this->calculatePercentageChange($expenseThisMonth, $expenseLastMonth);

        // --- PIUTANG (RECEIVABLES) ---
        // Piutang Bulan Ini (total sisa pembayaran dari invoice yang 'belum_lunas')
        $receivablesThisMonth = Payment::whereBetween('tanggal_pembayaran', [$startOfMonth, $endOfMonth])
            ->where('status', 'belum_lunas')
            ->sum('pembayaran');

        // Piutang Bulan Lalu
        $receivablesLastMonth = Payment::whereBetween('tanggal_pembayaran', [$startOfMonth, $endOfMonth])
            ->where('status', 'belum_lunas')
            ->sum('pembayaran');

        $receivablesChange = $this->calculatePercentageChange($receivablesThisMonth, $receivablesLastMonth);


        // --- LABA BERSIH (PROFIT) ---
        // Laba Bersih Bulan Ini
        $profitThisMonth = $incomeThisMonth - $expenseThisMonth;

        // Laba Bersih Bulan Lalu
        $profitLastMonth = $incomeLastMonth - $expenseLastMonth;

        $profitChange = $this->calculatePercentageChange($profitThisMonth, $profitLastMonth);


        // --- TAMPILAN WIDGET ---
        return [
            Stat::make('Laba Bersih', 'Rp ' . number_format($profitThisMonth, 0, ',', '.'))
                ->description(number_format(abs($profitChange), 1) . '% vs bulan lalu')
                ->descriptionIcon($profitChange >= 0 ? 'heroicon-m-arrow-trending-up' : 'heroicon-m-arrow-trending-down')
                ->color($profitChange >= 0 ? 'success' : 'danger'),

            Stat::make('Profit Garasi', 'Rp ' . number_format($incomeThisMonth, 0, ',', '.'))
                ->description(number_format(abs($incomeChange), 1) . '% vs bulan lalu')
                ->descriptionIcon($incomeChange >= 0 ? 'heroicon-m-arrow-trending-up' : 'heroicon-m-arrow-trending-down')
                ->color($incomeChange >= 0 ? 'success' : 'danger'),


            Stat::make('Total Pengeluaran', 'Rp ' . number_format($expenseThisMonth, 0, ',', '.'))
                ->description(number_format(abs($expenseChange), 1) . '% vs bulan lalu')
                // Logika terbalik: pengeluaran turun itu bagus (success), naik itu jelek (danger)
                ->descriptionIcon($expenseChange <= 0 ? 'heroicon-m-arrow-trending-up' : 'heroicon-m-arrow-trending-down')
                ->color($expenseChange <= 0 ? 'success' : 'danger'),

            Stat::make('Total Piutang', 'Rp ' . number_format($receivablesThisMonth, 0, ',', '.'))
                ->description(number_format(abs($receivablesChange), 1) . '% vs bulan lalu')
                ->descriptionIcon($receivablesChange > 0 ? 'heroicon-m-arrow-trending-up' : 'heroicon-m-arrow-trending-down')
                ->color($receivablesChange <= 0 ? 'success' : 'danger'),
            Stat::make('Total Pendapatan Sewa', 'Rp ' . number_format($revenueThisMonth, 0, ',', '.'))
                ->description(number_format(abs($revenueChange), 1) . '% vs bulan lalu')
                ->descriptionIcon($revenueChange > 0 ? 'heroicon-m-arrow-trending-up' : 'heroicon-m-arrow-trending-down')
                ->color($revenueChange <= 0 ? 'success' : 'danger'),
        ];
    }

    public static function canViewAny(): bool
    {
        // Hanya admin bisa akses
        return Auth::user()->isAdmin();
    }
}
