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
    protected int | string | array $columnSpan = 'full';

    // Pindahkan query ke getViewData()
    protected function getViewData(): array
    {
        $bookings = Booking::with(['car', 'customer', 'driver'])
            ->where('status', 'booking')
            ->whereDate('tanggal_keluar', \Carbon\Carbon::today())
            ->get();

        return [
            'bookings' => $bookings,
        ];
    }
}