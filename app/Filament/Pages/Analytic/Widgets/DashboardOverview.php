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

        //Pendapatan Kotor
        $RevenueMonth = Payment::where('status', 'lunas')
            ->whereBetween('tanggal_pembayaran', [$startOfMonth, $endOfMonth])
            // ->join('invoices', 'payments.invoice_id', '=', 'invoices.id')
            ->sum('pembayaran');
        $RevenueLastMonth = Payment::where('status', 'lunas')
            ->whereBetween('tanggal_pembayaran', [$startOfLastMonth, $endOfLastMonth])
            // ->join('invoices', 'payments.invoice_id', '=', 'invoices.id')
            ->sum('pembayaran');
        $RevenueChange = $this->calculatePercentageChange($RevenueMonth, $RevenueLastMonth);

        // Pemasukan Bulan Lalu
        $incomeLastMonth = Payment::whereBetween('tanggal_pembayaran', [$startOfLastMonth, $endOfLastMonth])
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
        $receivablesThisMonth = Payment::where('status', 'belum_lunas')
            ->whereBetween('tanggal_pembayaran', [$startOfMonth, $endOfMonth])
            ->join('invoices', 'payments.invoice_id', '=', 'invoices.id')
            ->sum('invoices.sisa_pembayaran');

        // Piutang Bulan Lalu
        $receivablesLastMonth = Payment::where('status', 'belum_lunas')
            ->whereBetween('tanggal_pembayaran', [$startOfLastMonth, $endOfLastMonth])
            ->join('invoices', 'payments.invoice_id', '=', 'invoices.id')
            ->sum('invoices.sisa_pembayaran');

        $receivablesChange = $this->calculatePercentageChange($receivablesThisMonth, $receivablesLastMonth);


        // --- LABA BERSIH (PROFIT) ---
        // Laba Bersih Bulan Ini
        $profitThisMonth = $incomeThisMonth - $expenseThisMonth;

        // Laba Bersih Bulan Lalu
        $profitLastMonth = $incomeLastMonth - $expenseLastMonth;

        $profitChange = $this->calculatePercentageChange($profitThisMonth, $profitLastMonth);
        // --- KALKULASI UTILISASI ARMADA (VERSI AKURAT) ---

        // 1. Hitung SEMUA mobil aktif di garasi SPT (Denominator)
        $jumlahMobilSPT = \App\Models\Car::where('garasi', 'SPT')->count();

        // Tentukan periode bulan ini
        $startOfMonth = now()->startOfMonth();
        $endOfMonth = now()->endOfMonth();
        $jumlahHariDalamBulan = $startOfMonth->daysInMonth;

        // Total hari kapasitas armada SPT bulan ini
        $totalHariTersedia = $jumlahMobilSPT * $jumlahHariDalamBulan;


        // 2. Hitung total hari disewa yang HANYA jatuh di bulan ini (Numerator)
        $totalHariDisewa = 0;

        // Ambil semua booking yang AKTIF (beririsan) di bulan ini
        $bookingsBulanIni = \App\Models\Booking::whereHas('car', function ($query) {
            $query->where('garasi', 'SPT');
        })
            ->where('tanggal_keluar', '<=', $endOfMonth) // Booking yg dimulai sebelum akhir bulan ini
            ->where('tanggal_kembali', '>=', $startOfMonth) // dan berakhir setelah awal bulan ini
            ->get();

        foreach ($bookingsBulanIni as $booking) {
            // Tentukan tanggal mulai dan selesai booking dalam rentang bulan ini
            $actualStart = max(Carbon::parse($booking->tanggal_keluar), $startOfMonth);
            $actualEnd = min(Carbon::parse($booking->tanggal_kembali), $endOfMonth);

            // Hitung selisih hari dan tambahkan ke total (tambah 1 karena inklusif)
            $totalHariDisewa += $actualStart->diffInDays($actualEnd);
        }


        // 3. Kalkulasi final
        $rawUtilization = ($totalHariTersedia > 0) ? ($totalHariDisewa / $totalHariTersedia) * 100 : 0;
        $utilizationRate = round($rawUtilization); // Dibulatkan di sini

        // --- TAMPILAN WIDGET ---
        return [
            Stat::make('Aktivitas Armada SPT', number_format($utilizationRate, 0) . '%')
                ->icon('heroicon-o-key')
                ->description(round($totalHariDisewa) . " dari {$totalHariTersedia} Total Hari Disewa")
                ->color('success'),
            Stat::make('Total Piutang', 'Rp ' . number_format($receivablesThisMonth, 0, ',', '.'))
                ->icon('heroicon-o-clock') // IKON DITAMBAHKAN
                ->description(number_format(abs($receivablesChange), 1) . '% vs bulan lalu')
                ->descriptionIcon($receivablesChange <= 0 ? 'heroicon-m-arrow-trending-down' : 'heroicon-m-arrow-trending-up')
                ->color($receivablesChange <= 0 ? 'success' : 'danger'), // Logika terbalik: piutang turun itu bagus
            Stat::make('Pendapatan Kotor', 'Rp ' . number_format($RevenueMonth, 0, ',', '.'))
                ->icon('heroicon-o-arrow-trending-up') // IKON DITAMBAHKAN
                ->description(number_format(abs($RevenueChange), 1) . '% vs bulan lalu')
                ->descriptionIcon($RevenueChange >= 0 ? 'heroicon-m-arrow-trending-up' : 'heroicon-m-arrow-trending-down')
                ->color($RevenueChange >= 0 ? 'success' : 'danger'),
            Stat::make('Profit Garasi', 'Rp ' . number_format($incomeThisMonth, 0, ',', '.'))
                ->icon('heroicon-o-chart-bar-square') // IKON DITAMBAHKAN
                ->description(number_format(abs($incomeChange), 1) . '% vs bulan lalu')
                ->descriptionIcon($incomeChange >= 0 ? 'heroicon-m-arrow-trending-up' : 'heroicon-m-arrow-trending-down')
                ->color($incomeChange >= 0 ? 'success' : 'danger'),

            Stat::make('Total Pengeluaran', 'Rp ' . number_format($expenseThisMonth, 0, ',', '.'))
                ->description(number_format(abs($expenseChange), 1) . '% vs bulan lalu')
                // Logika terbalik: pengeluaran turun itu bagus (success), naik itu jelek (danger)
                ->icon('heroicon-o-arrow-trending-down')
                ->descriptionIcon($expenseChange <= 0 ? 'heroicon-m-arrow-trending-up' : 'heroicon-m-arrow-trending-down')
                ->color($expenseChange <= 0 ? 'success' : 'danger'),
            Stat::make('Laba Bersih', 'Rp ' . number_format($profitThisMonth, 0, ',', '.'))
                ->icon('heroicon-o-banknotes') // IKON DITAMBAHKAN
                ->description(number_format(abs($profitChange), 1) . '% vs bulan lalu')
                ->descriptionIcon($profitChange >= 0 ? 'heroicon-m-arrow-trending-up' : 'heroicon-m-arrow-trending-down')
                ->color($profitChange >= 0 ? 'success' : 'danger'),


            // Logika terbalik: piutang turun itu bagus


        ];
    }

    public static function canViewAny(): bool
    {
        // Hanya admin bisa akses
        return Auth::user()->isAdmin();
    }
}
