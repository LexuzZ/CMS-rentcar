<?php

namespace App\Filament\Resources\BookingResource\Widgets;

use App\Models\Booking;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class BookingStats extends BaseWidget
{
    protected function getStats(): array
    {
        return [
            Stat::make('Total Booking', Booking::count())
                ->description('Semua data booking')
                ->color('primary'),

            Stat::make('Sedang Booking', Booking::where('status', 'booking')->count())
                ->description('Belum diproses')
                ->color('info'),

            Stat::make('Disewa', Booking::where('status', 'disewa')->count())
                ->description('Unit sedang jalan')
                ->color('warning'),

            Stat::make('Selesai', Booking::where('status', 'selesai')->count())
                ->description('Transaksi selesai')
                ->color('success'),

            Stat::make('Batal', Booking::where('status', 'batal')->count())
                ->description('Pesanan dibatalkan')
                ->color('danger'),
        ];
    }
}
