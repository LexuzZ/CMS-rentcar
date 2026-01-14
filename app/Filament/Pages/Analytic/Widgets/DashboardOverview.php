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

    // protected ?string $description = 'An overview of some analytics.';

    protected static ?int $sort = 1;



    /**
     * Helper function untuk menghitung perubahan persentase.
     *
     * @param float $current
     * @param float $previous
     * @return float
     */
    private function calculatePercentageChange(float $current, float $previous): float
    {
        if ($previous == 0) {
            return $current > 0 ? 100 : 0;
        }
        return (($current - $previous) / $previous) * 100;
    }

    protected function getStats(): array
    {
        $startOfMonth = now()->startOfMonth();
        $endOfMonth = now()->endOfMonth();
        $startOfLastMonth = now()->subMonth()->startOfMonth();
        $endOfLastMonth = now()->subMonth()->endOfMonth();

        /**
         * ===============================
         * PROFIT GARASI (LABA KOTOR)
         * ===============================
         */
        $incomeThisMonth = Payment::whereBetween('tanggal_pembayaran', [$startOfMonth, $endOfMonth])
            ->whereHas('invoice', fn($q) => $q->where('status', 'lunas'))
            ->with('invoice.booking.car')
            ->get()
            ->sum(function ($payment) {
                $days = $payment->invoice->booking->total_hari;
                $car = $payment->invoice->booking->car;

                return ($car->harga_harian - $car->harga_pokok) * $days;
            });

        $incomeLastMonth = Payment::whereBetween('tanggal_pembayaran', [$startOfLastMonth, $endOfLastMonth])
            ->whereHas('invoice', fn($q) => $q->where('status', 'lunas'))
            ->with('invoice.booking.car')
            ->get()
            ->sum(function ($payment) {
                $days = $payment->invoice->booking->total_hari;
                $car = $payment->invoice->booking->car;

                return ($car->harga_harian - $car->harga_pokok) * $days;
            });

        $incomeChange = $this->calculatePercentageChange($incomeThisMonth, $incomeLastMonth);

        /**
         * ===============================
         * PENDAPATAN KOTOR
         * ===============================
         */
        $RevenueMonth = Payment::whereBetween('tanggal_pembayaran', [$startOfMonth, $endOfMonth])
            ->whereHas('invoice', fn($q) => $q->where('status', 'lunas'))
            ->sum('pembayaran');

        $RevenueLastMonth = Payment::whereBetween('tanggal_pembayaran', [$startOfLastMonth, $endOfLastMonth])
            ->whereHas('invoice', fn($q) => $q->where('status', 'lunas'))
            ->sum('pembayaran');

        $RevenueChange = $this->calculatePercentageChange($RevenueMonth, $RevenueLastMonth);

        /**
         * ===============================
         * PENGELUARAN
         * ===============================
         */
        $expenseThisMonth = Pengeluaran::whereBetween('tanggal_pengeluaran', [$startOfMonth, $endOfMonth])
            ->sum('pembayaran');

        $expenseLastMonth = Pengeluaran::whereBetween('tanggal_pengeluaran', [$startOfLastMonth, $endOfLastMonth])
            ->sum('pembayaran');

        $expenseChange = $this->calculatePercentageChange($expenseThisMonth, $expenseLastMonth);

        /**
         * ===============================
         * PIUTANG
         * ===============================
         */
        $receivablesThisMonth = Payment::whereBetween('tanggal_pembayaran', [$startOfMonth, $endOfMonth])
            ->whereHas('invoice', fn($q) => $q->where('status', 'belum_lunas'))
            ->sum('pembayaran');

        $receivablesLastMonth = Payment::whereBetween('tanggal_pembayaran', [$startOfLastMonth, $endOfLastMonth])
            ->whereHas('invoice', fn($q) => $q->where('status', 'belum_lunas'))
            ->sum('pembayaran');

        $receivablesChange = $this->calculatePercentageChange($receivablesThisMonth, $receivablesLastMonth);

        /**
         * ===============================
         * LABA BERSIH
         * ===============================
         */
        $profitThisMonth = $RevenueMonth - $expenseThisMonth;
        $profitLastMonth = $RevenueLastMonth - $expenseLastMonth;
        $profitChange = $this->calculatePercentageChange($profitThisMonth, $profitLastMonth);

        /**
         * ===============================
         * UTILISASI ARMADA
         * ===============================
         */
        $jumlahMobilSPT = \App\Models\Car::where('garasi', 'SPT')->count();
        $daysInMonth = $startOfMonth->daysInMonth;
        $totalHariTersedia = $jumlahMobilSPT * $daysInMonth;

        $totalHariDisewa = \App\Models\Booking::whereHas('car', fn($q) => $q->where('garasi', 'SPT'))
            ->where('tanggal_keluar', '<=', $endOfMonth)
            ->where('tanggal_kembali', '>=', $startOfMonth)
            ->get()
            ->sum(function ($booking) use ($startOfMonth, $endOfMonth) {
                $start = max(Carbon::parse($booking->tanggal_keluar), $startOfMonth);
                $end = min(Carbon::parse($booking->tanggal_kembali), $endOfMonth);
                return $start->diffInDays($end);
            });

        $utilizationRate = $totalHariTersedia > 0
            ? ($totalHariDisewa / $totalHariTersedia) * 100
            : 0;

        /**
         * ===============================
         * WIDGET OUTPUT
         * ===============================
         */
        return [
            Stat::make('Aktivitas Armada SPT', number_format($utilizationRate, 0) . '%')
                ->icon('heroicon-o-key')
                ->description("$totalHariDisewa dari $totalHariTersedia hari"),

            Stat::make('Total Piutang', 'Rp ' . number_format($receivablesThisMonth, 0, ',', '.'))
                ->description(number_format(abs($receivablesChange), 1) . '% vs bulan lalu')
                ->color($receivablesChange <= 0 ? 'success' : 'danger'),

            Stat::make('Pendapatan Kotor', 'Rp ' . number_format($RevenueMonth, 0, ',', '.'))
                ->description(number_format(abs($RevenueChange), 1) . '% vs bulan lalu')
                ->color($RevenueChange >= 0 ? 'success' : 'danger'),

            Stat::make('Profit Garasi', 'Rp ' . number_format($incomeThisMonth, 0, ',', '.'))
                ->description(number_format(abs($incomeChange), 1) . '% vs bulan lalu')
                ->color($incomeChange >= 0 ? 'success' : 'danger'),

            Stat::make('Total Pengeluaran', 'Rp ' . number_format($expenseThisMonth, 0, ',', '.'))
                ->description(number_format(abs($expenseChange), 1) . '% vs bulan lalu')
                ->color($expenseChange <= 0 ? 'success' : 'danger'),

            Stat::make('Laba Bersih', 'Rp ' . number_format($profitThisMonth, 0, ',', '.'))
                ->description(number_format(abs($profitChange), 1) . '% vs bulan lalu')
                ->color($profitChange >= 0 ? 'success' : 'danger'),
        ];
    }


    public static function canViewAny(): bool
    {
        // Hanya admin bisa akses
        return Auth::user()->isAdmin();
    }
}
