<?php

namespace App\Filament\Pages\Analytic\Widgets;

use App\Models\Invoice;
use App\Models\Payment;
use App\Models\Penalty;
use App\Models\Pengeluaran;
use Carbon\Carbon;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\Auth;

class DashboardOverview extends BaseWidget
{
    protected function getStats(): array
    {
        $startOfMonth = Carbon::now()->startOfMonth();
        $endOfMonth = Carbon::now()->endOfMonth();

        // ================= BULAN INI =================
        // Pendapatan bulan ini (hanya invoice LUNAS)
        $totalRevenueMonth = Payment::whereBetween('tanggal_pembayaran', [$startOfMonth, $endOfMonth])
            ->where('status', 'lunas')
            ->get()
            ->sum(function ($payment) {
                $totalDays = $payment->invoice->booking->total_hari;
                $hargaPokokTotal = $payment->invoice->booking->car->harga_pokok * $totalDays;
                $hargaHarianTotal = $payment->invoice->booking->car->harga_harian * $totalDays;
                return $hargaHarianTotal - $hargaPokokTotal;
            });
        $totalNetGarasi = Payment::where('status', 'lunas')
            ->get()
            ->sum(function ($payment) {
                $totalDays = $payment->invoice->booking->total_hari;
                $hargaPokokTotal = $payment->invoice->booking->car->harga_pokok * $totalDays;
                $hargaHarianTotal = $payment->invoice->booking->car->harga_harian * $totalDays;
                return $hargaHarianTotal - $hargaPokokTotal;
            });

        $ongkir = Invoice::whereBetween('created_at', [$startOfMonth, $endOfMonth])
            ->sum('pickup_dropOff');
        $klaimBbm = Penalty::where('klaim', 'bbm')
            ->whereBetween('created_at', [$startOfMonth, $endOfMonth])
            ->sum('amount');
        $klaimOvertime = Penalty::where('klaim', 'overtime')
            ->whereBetween('created_at', [$startOfMonth, $endOfMonth])
            ->sum('amount');
        $klaimBaret = Penalty::where('klaim', 'baret')
            ->whereBetween('created_at', [$startOfMonth, $endOfMonth])
            ->sum('amount');
        $klaimOverland = Penalty::where('klaim', 'overland')
            ->whereBetween('created_at', [$startOfMonth, $endOfMonth])
            ->sum('amount');

        // Pengeluaran bulan ini
        $totalExpenseMonth = Pengeluaran::whereBetween('tanggal_pengeluaran', [$startOfMonth, $endOfMonth])
            ->sum('pembayaran');


        // Piutang bulan ini (belum lunas)
        $RevenueMonth = Payment::whereBetween('tanggal_pembayaran', [$startOfMonth, $endOfMonth])
            ->where('status', 'lunas')
            ->sum('pembayaran');
        $piutangMonth = Payment::where('payments.status', 'belum_lunas')
            ->whereBetween('payments.tanggal_pembayaran', [$startOfMonth, $endOfMonth])
            ->join('invoices', 'payments.invoice_id', '=', 'invoices.id')
            ->sum('invoices.sisa_pembayaran');

        // Laba bulan ini
        $profitMonth = $totalRevenueMonth - $totalExpenseMonth;

        // ================= KESELURUHAN =================
        // Total Pendapatan (all time)
        $totalRevenueAll = Payment::where('status', 'lunas')->sum('pembayaran');

        // Total Pengeluaran (all time)
        $totalExpenseAll = Pengeluaran::sum('pembayaran');

        // Total Piutang (all time)
        $piutangAll = Payment::where('status', 'belum_lunas')->join('invoices', 'payments.invoice_id', '=', 'invoices.id')
            ->sum('invoices.sisa_pembayaran');

        // Total Laba (all time)
        $profitAll = $totalNetGarasi - $totalExpenseAll;

        return [
            // ===== BULAN INI =====
            Stat::make('Profit Garasi Bulan Ini', 'Rp ' . number_format($totalRevenueMonth, 0, ',', '.'))
                ->description('Total pemasukan dari Profit Marketing bulan ini')
                ->descriptionIcon('heroicon-m-arrow-trending-up')
                ->color('success'),
            Stat::make('Profit Garasi Keseluruhan', 'Rp ' . number_format($totalNetGarasi, 0, ',', '.'))
                ->description('Total pemasukan dari Profit Marketing Keseluruhan')
                ->descriptionIcon('heroicon-m-arrow-trending-up')
                ->color('success'),
            Stat::make('Biaya Pengantaran Bulan Ini', 'Rp ' . number_format($ongkir, 0, ',', '.'))
                ->description('Total Biaya Pengantaran bulan ini')
                ->color('danger'),
            Stat::make('Klaim BBM Bulan Ini', 'Rp ' . number_format($klaimBbm, 0, ',', '.'))
                ->description('Total Klaim BBM bulan ini')
                ->color('success'),
            Stat::make('Pendapatan Sewa Bulan Ini', 'Rp ' . number_format($RevenueMonth, 0, ',', '.'))
                ->description('Total pemasukan dari Sewa bulan ini')
                ->descriptionIcon('heroicon-m-arrow-trending-up')
                ->color('success'),
            // ===== KESELURUHAN =====
            Stat::make('Total Pendapatan Sewa', 'Rp ' . number_format($totalRevenueAll, 0, ',', '.'))
                ->description('Total pemasukan keseluruhan')
                ->descriptionIcon('heroicon-m-arrow-trending-up')
                ->color('success'),
            Stat::make('Klaim Baret Bulan Ini', 'Rp ' . number_format($klaimBaret, 0, ',', '.'))
                ->description('Total Klaim Baret bulan ini')
                ->color('success'),
            Stat::make('Total Piutang Bulan Ini', 'Rp ' . number_format($piutangMonth, 0, ',', '.'))
                ->description('Sisa pembayaran dari invoice belum lunas bulan ini')
                ->color('warning'),
            Stat::make('Total Piutang', 'Rp ' . number_format($piutangAll, 0, ',', '.'))
                ->description('Total pembayaran belum lunas keseluruhan')
                ->color('warning'),
            Stat::make('Klaim Overtime Bulan Ini', 'Rp ' . number_format($klaimOvertime, 0, ',', '.'))
                ->description('Total Klaim Overtime bulan ini')
                ->color('success'),
            Stat::make('Kas Keluar Bulan Ini', 'Rp ' . number_format($totalExpenseMonth, 0, ',', '.'))
                ->description('Total biaya operasional bulan ini')
                ->descriptionIcon('heroicon-m-arrow-trending-down')
                ->color('danger'),
            Stat::make('Total Kas Keluar', 'Rp ' . number_format($totalExpenseAll, 0, ',', '.'))
                ->description('Total biaya operasional keseluruhan')
                ->descriptionIcon('heroicon-m-arrow-trending-down')
                ->color('danger'),
            Stat::make('Klaim Overland Bulan Ini', 'Rp ' . number_format($klaimOverland, 0, ',', '.'))
                ->description('Total Klaim Overland bulan ini')
                ->color('success'),
            Stat::make('Laba Bersih Bulan Ini', 'Rp ' . number_format($profitMonth, 0, ',', '.'))
                ->description('Pendapatan - Pengeluaran bulan ini')
                ->color($profitMonth >= 0 ? 'success' : 'danger'),
            Stat::make('Total Laba Bersih', 'Rp ' . number_format($profitAll, 0, ',', '.'))
                ->description('Pendapatan - Pengeluaran keseluruhan')
                ->color($profitAll >= 0 ? 'success' : 'danger'),






        ];
    }

    public static function canViewAny(): bool
    {
        // Hanya admin bisa akses
        return Auth::user()->isAdmin();
    }
}
