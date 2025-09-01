<?php

namespace App\Filament\Pages\Analytic\Widgets;

use App\Models\Payment;
use App\Models\Pengeluaran;
use Carbon\Carbon;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class DashboardOverview extends BaseWidget
{
    protected function getStats(): array
    {
        $startOfMonth = Carbon::now()->startOfMonth();
        $endOfMonth   = Carbon::now()->endOfMonth();

        // ================= BULAN INI =================
        // Pendapatan bulan ini (hanya invoice LUNAS)
        $totalRevenueMonth = Payment::whereBetween('tanggal_pembayaran', [$startOfMonth, $endOfMonth])
            ->where('status', 'lunas')
            ->sum('pembayaran');

        // Pengeluaran bulan ini
        $totalExpenseMonth = Pengeluaran::whereBetween('tanggal_pengeluaran', [$startOfMonth, $endOfMonth])
            ->sum('pembayaran');

        // Piutang bulan ini (belum lunas)
        $piutangMonth = Payment::whereBetween('tanggal_pembayaran', [$startOfMonth, $endOfMonth])
            ->where('status', 'belum_lunas')
            ->sum('pembayaran');

        // Laba bulan ini
        $profitMonth = $totalRevenueMonth - $totalExpenseMonth;

        // ================= KESELURUHAN =================
        // Total Pendapatan (all time)
        $totalRevenueAll = Payment::where('status', 'lunas')->sum('pembayaran');

        // Total Pengeluaran (all time)
        $totalExpenseAll = Pengeluaran::sum('pembayaran');

        // Total Piutang (all time)
        $piutangAll = Payment::where('status', 'belum_lunas')->sum('pembayaran');

        // Total Laba (all time)
        $profitAll = $totalRevenueAll - $totalExpenseAll;

        return [
            // ===== BULAN INI =====
            Stat::make('Kas Masuk Bulan Ini', 'Rp ' . number_format($totalRevenueMonth, 0, ',', '.'))
                ->description('Total pemasukan dari pembayaran bulan ini')
                ->color('success'),

            Stat::make('Kas Keluar Bulan Ini', 'Rp ' . number_format($totalExpenseMonth, 0, ',', '.'))
                ->description('Total biaya operasional bulan ini')
                ->color('danger'),

            Stat::make('Piutang Bulan Ini', 'Rp ' . number_format($piutangMonth, 0, ',', '.'))
                ->description('Pembayaran belum lunas bulan ini')
                ->color('warning'),

            Stat::make('Laba Bersih Bulan Ini', 'Rp ' . number_format($profitMonth, 0, ',', '.'))
                ->description('Pendapatan - Pengeluaran bulan ini')
                ->color($profitMonth >= 0 ? 'success' : 'danger'),

            // ===== KESELURUHAN =====
            Stat::make('Total Pendapatan', 'Rp ' . number_format($totalRevenueAll, 0, ',', '.'))
                ->description('Total pemasukan keseluruhan')
                ->color('success'),

            Stat::make('Total Pengeluaran', 'Rp ' . number_format($totalExpenseAll, 0, ',', '.'))
                ->description('Total biaya operasional keseluruhan')
                ->color('danger'),

            Stat::make('Total Piutang', 'Rp ' . number_format($piutangAll, 0, ',', '.'))
                ->description('Total pembayaran belum lunas keseluruhan')
                ->color('warning'),

            Stat::make('Total Laba Bersih', 'Rp ' . number_format($profitAll, 0, ',', '.'))
                ->description('Pendapatan - Pengeluaran keseluruhan')
                ->color($profitAll >= 0 ? 'success' : 'danger'),
        ];
    }

    public static function canViewAny(): bool
    {
        // Hanya admin bisa akses
        return auth()->user()->isAdmin();
    }
}
