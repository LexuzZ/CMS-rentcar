<?php

namespace App\Filament\Widgets;

use App\Models\Tempo;
use Filament\Widgets\Widget;

class TempoDueToday extends Widget
{
    // Arahkan ke file Blade yang akan kita modifikasi
    protected static string $view = 'filament.widgets.tempo-due-today-cards';

    // Ubah judul widget agar lebih sesuai
    protected static ?string $heading = 'Jadwal Perawatan Mendatang';

    protected int|string|array $columnSpan = 'full';

    // Method untuk mengambil dan mengirim data ke view
    protected function getViewData(): array
    {
        $tempos = Tempo::query()
            ->with(['car.carModel.brand']) // Eager load untuk efisiensi
            // Ambil semua tempo yang jatuh tempo hari ini atau di masa depan
            ->where('jatuh_tempo', '>=', today())
            // Urutkan dari yang paling dekat tanggalnya
            ->orderBy('jatuh_tempo', 'asc')
            ->get();

        return [
            'tempos' => $tempos, // Kirim data dengan nama 'tempos'
        ];
    }
}
