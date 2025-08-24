<?php

namespace App\Filament\Widgets;

use App\Models\Booking;
use Filament\Widgets\Widget; // <-- Ganti menjadi Widget dasar

class MobilKembali extends Widget
{
    // 1. Definisikan file Blade yang akan kita gunakan
    protected static string $view = 'filament.widgets.mobil-kembali-card';

    protected static ?string $heading = 'Mobil Kembali Hari Ini';
    protected static ?int $sort = 3;
    protected int | string | array $columnSpan = 'full';

    // 2. Method untuk mengambil dan mengirim data ke view
    protected function getViewData(): array
    {
        $today = \Carbon\Carbon::today();
        $tomorrow = \Carbon\Carbon::tomorrow();

        $bookings = Booking::with(['car.carModel.brand', 'customer', 'driver'])
            ->where('status', 'booking')
            ->whereDate('tanggal_kembali', '>=', $today)
            ->whereDate('tanggal_kembali', '<=', $tomorrow) // ambil hari ini & besok
            ->orderBy('tanggal_kembali')
            ->orderBy('waktu_kembali')
            ->get();

        return [
            'bookings' => $bookings,
        ];
    }
}
