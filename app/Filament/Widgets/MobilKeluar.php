<?php

// Ganti namespace jika Anda memindahkannya
namespace App\Filament\Widgets;

use App\Models\Booking;
use Filament\Widgets\Widget;

// Ganti nama kelasnya
class MobilKeluar extends Widget
{
    // Tentukan file view yang akan kita buat
    protected static string $view = 'filament.widgets.mobil-keluar-card';

    // Properti dari widget lama Anda
    protected static ?string $heading = 'Mobil Keluar Hari Ini';
    protected static ?int $sort = 2;
    protected int|string|array $columnSpan = 'full';

    // Pindahkan query ke getViewData()
    protected function getViewData(): array
    {
        $today = \Carbon\Carbon::today();
        $tomorrow = \Carbon\Carbon::tomorrow();

        $bookings = Booking::with(['car.carModel.brand', 'customer', 'driver'])
            ->where('status', 'booking')
            ->whereDate('tanggal_keluar', '>=', $today)
            ->whereDate('tanggal_keluar', '<=', $tomorrow) // ambil hari ini & besok
            ->orderBy('tanggal_keluar')
            ->orderBy('waktu_keluar')
            ->get();

        return [
            'bookings' => $bookings,
        ];
    }

}
