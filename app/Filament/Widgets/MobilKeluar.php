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
    protected static ?string $heading = 'Mobil Keluar Hari Ini & Besok';
    protected static ?int $sort = 2;
    protected int|string|array $columnSpan = 'full';

    // Pindahkan query ke getViewData()
    protected function getViewData(): array
    {
        $today = \Carbon\Carbon::today('Asia/Jakarta');
        $tomorrow = \Carbon\Carbon::tomorrow('Asia/Jakarta');

        // Mengambil data untuk hari ini
        $bookingsToday = Booking::with(['car.carModel.brand', 'customer', 'driver'])
            ->where('status', 'booking')
            ->whereDate('tanggal_keluar', $today)
            ->orderBy('waktu_keluar')
            ->get();

        // Mengambil data untuk besok
        $bookingsTomorrow = Booking::with(['car.carModel.brand', 'customer', 'driver'])
            ->where('status', 'booking')
            ->whereDate('tanggal_keluar', $tomorrow)
            ->orderBy('waktu_keluar')
            ->get();

        return [
            'bookingsToday' => $bookingsToday,
            'bookingsTomorrow' => $bookingsTomorrow,
        ];
    }
}
