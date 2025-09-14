<?php

namespace App\Filament\Resources\BookingResource\Widgets;

use App\Models\Booking;
use Carbon\Carbon;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class BookingStats extends BaseWidget
{
    protected function getStats(): array
    {
        $now = Carbon::now();
        return [
            Stat::make('Total Booking Bulan Ini', Booking::whereMonth('tanggal_keluar', $now->month)
                ->whereYear('tanggal_keluar', $now->year)
                ->count())
                ->description('Semua booking bulan ini')
                ->color('primary'),
            Stat::make('Booking', Booking::where('status', 'booking')
                ->whereMonth('tanggal_keluar', $now->month)
                ->whereYear('tanggal_keluar', $now->year)
                ->count())
                ->description('Belum diproses')
                ->color('info'),

            Stat::make('Disewa', Booking::where('status', 'disewa')
                ->whereMonth('tanggal_keluar', $now->month)
                ->whereYear('tanggal_keluar', $now->year)
                ->count())
                ->description('Unit sedang jalan')
                ->color('warning'),

            Stat::make('Selesai', Booking::where('status', 'selesai')
                ->whereMonth('tanggal_keluar', $now->month)
                ->whereYear('tanggal_keluar', $now->year)
                ->count())
                ->description('Transaksi selesai')
                ->color('success'),
        ];
    }
}
