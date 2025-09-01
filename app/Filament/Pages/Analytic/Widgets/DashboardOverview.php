<?php

namespace App\Filament\Pages\Analytic\Widgets;

use App\Models\Booking;
use App\Models\Car;
use App\Models\Invoice;
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
        $today = Carbon::today();
        $returnsToday = Booking::whereDate('tanggal_kembali', $today)->count();

        // Pemesanan mulai hari ini
        $bookingsToday = Booking::whereDate('tanggal_keluar', $today)->count();

        // Mobil dengan status "Ready"
        $carsAvailable = Car::where('status', 'Ready')->count();
        $mostBookedCar = Car::withCount('bookings')
            ->orderByDesc('bookings_count')
            ->first();

        // Mobil dengan status "Disewa"
        $carsRented = Car::where('status', 'Disewa')->count();
        // $invoiceCount = Invoice::where('status', 'Belum Lunas')->count();

        $now = now();
        $currentMonth = $now->month;
        $currentYear = $now->year;
        $lastMonth = $now->copy()->subMonth();
        $monthName = Carbon::create()->month($currentMonth)->locale('id')->isoFormat('MMMM');

        // 💰 Total Pendapatan Bulan Ini (Sewa + Denda)
        $total = Invoice::whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->where('status', 'lunas') // kalau ada status paid
            ->sum('total_amount');


        $totalPenalty = Penalty::whereMonth('created_at', $currentMonth)
            ->whereYear('created_at', $currentYear)
            ->sum('amount');

        $totalRevenue = $total + $totalPenalty;
        $totalPengeluaranBulanIni = Pengeluaran::whereMonth('created_at', $currentMonth)
            ->whereYear('created_at', $currentYear)
            ->sum('pembayaran');

        $pendapatanBersih = $totalRevenue - $totalPengeluaranBulanIni;
        // 🧾 Pendapatan Keseluruhan (tanpa filter bulan)
        $totalInvoiceKeseluruhan = Invoice::sum('total');
        $totalPenaltyKeseluruhan = Penalty::sum('amount');
        $totalRevenueKeseluruhan = $totalInvoiceKeseluruhan + $totalPenaltyKeseluruhan;

        // 💸 Pengeluaran Keseluruhan
        $totalPengeluaranKeseluruhan = Pengeluaran::sum('pembayaran');

        // 📊 Pendapatan Bersih Keseluruhan
        $pendapatanBersihKeseluruhan = $totalRevenueKeseluruhan - $totalPengeluaranKeseluruhan;

        // 🔢 Total Denda Saja
        $totalPenaltyOnly = $totalPenalty;

        // 🚗 Jumlah Mobil Disewa Bulan Ini
        $carsRented = Booking::whereMonth('tanggal_keluar', $currentMonth)
            ->whereYear('tanggal_keluar', $currentYear)
            ->count();

        // 📈 Perbandingan Pendapatan dengan Bulan Lalu
        $lastMonthInvoice = Invoice::whereMonth('tanggal_invoice', $lastMonth->month)
            ->whereYear('tanggal_invoice', $lastMonth->year)
            ->sum('total');

        $lastMonthPenalty = Penalty::whereMonth('created_at', $lastMonth->month)
            ->whereYear('created_at', $lastMonth->year)
            ->sum('amount');

        $lastMonthRevenue = $lastMonthInvoice + $lastMonthPenalty;

        $difference = $totalRevenue - $lastMonthRevenue;
        $percentageChange = $lastMonthRevenue == 0 ? 100 : round(($difference / $lastMonthRevenue) * 100);


        return [
            Stat::make('Pemesanan Hari Ini', $bookingsToday)
                ->description('Pemesanan yang dimulai hari ini')
                ->icon('heroicon-o-calendar')
                ->color('primary'),
            Stat::make('Mobil Tersedia', $carsAvailable)
                ->description('Status Ready')
                ->icon('heroicon-o-check-circle')
                ->color('success'),
            Stat::make('Mobil Disewa', $carsRented)
                ->description("Jumlah Booking Bulan Ini")
                ->icon('heroicon-o-truck')
                ->color('primary'),
            Stat::make('Mobil Kembali Hari Ini', $returnsToday)
                ->description('Jumlah mobil yang kembali hari ini')
                ->icon('heroicon-o-bell-alert')
                ->color($returnsToday > 0 ? 'warning' : 'gray'),
            Stat::make('Mobil Disewa', $carsRented)
                ->description('Sedang digunakan')
                ->color('warning'),
            // Stat::make('Mobil Terpopuler', $mostBookedCar?->nama_mobil ?? '-')
            //     ->description('Paling banyak disewa (' . ($mostBookedCar?->bookings_count ?? 0) . 'x)')
            //     ->icon('heroicon-o-star')
            //     ->color('info'),
            Stat::make('Pengeluaran Bulan Ini', number_format($totalPengeluaranBulanIni, 0, ',', '.'))
                // ->description('Total Pengeluaran')
                ->description($monthName . ' ' . $currentYear)
                ->icon('heroicon-o-currency-dollar')
                ->color('danger'),
            Stat::make('Pendapatan Bulan Ini', 'Rp ' . number_format($totalRevenue, 0, ',', '.'))
                ->description($monthName . ' ' . $currentYear)
                ->icon('heroicon-o-currency-dollar')
                ->color('success'),
            Stat::make('Total Denda', 'Rp ' . number_format($totalPenaltyOnly, 0, ',', '.'))
                ->description("Denda di bulan $monthName")
                ->icon('heroicon-o-exclamation-triangle')
                ->color('danger'),
            // Stat::make('Pendapatan Bersih Bulan Ini', 'Rp ' . number_format($pendapatanBersih, 0, ',', '.'))
            //     ->description("Setelah dikurangi pengeluaran $monthName")
            //     ->icon('heroicon-o-banknotes')
            //     ->color($pendapatanBersih >= 0 ? 'success' : 'danger'),
            Stat::make('Gross Revenue', 'Rp ' . number_format($totalRevenueKeseluruhan, 0, ',', '.'))
                ->description('Total pendapatan sewa + denda')
                ->icon('heroicon-o-currency-dollar')
                ->color('success'),
            Stat::make('Gross Expenses', 'Rp ' . number_format($totalPengeluaranKeseluruhan, 0, ',', '.'))
                ->description('Total pengeluaran selama ini')
                ->icon('heroicon-o-banknotes')
                ->color('danger'),
            Stat::make('Overall Net Income', 'Rp ' . number_format($pendapatanBersihKeseluruhan, 0, ',', '.'))
                ->description('Pendapatan - pengeluaran')
                ->icon('heroicon-o-chart-bar')
                ->color($pendapatanBersihKeseluruhan >= 0 ? 'success' : 'danger'),
            Stat::make('Selisih Pendapatan', ($difference >= 0 ? '+Rp ' : '-Rp ') . number_format(abs($difference), 0, ',', '.'))
                ->description("Perubahan vs Bulan Lalu ($percentageChange%)")
                ->icon($difference >= 0 ? 'heroicon-o-arrow-trending-up' : 'heroicon-o-arrow-trending-down')
                ->color($difference >= 0 ? 'success' : 'danger'),

        ];
    }
    public static function canViewAny(): bool
    {
        // Hanya pengguna dengan peran 'admin' yang bisa melihat resource ini
        return Auth::user()->isAdmin();
    }
}
