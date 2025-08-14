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
        $bookings = Booking::with(['car', 'customer', 'driver'])
            ->where('status', 'disewa')
            ->whereDate('tanggal_kembali', \Carbon\Carbon::today())
            ->get();

        return [
            'bookings' => $bookings, // Kirim data dengan nama 'bookings'
        ];
    }
}
