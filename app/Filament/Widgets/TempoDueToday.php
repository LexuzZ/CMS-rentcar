<?php

namespace App\Filament\Widgets;

use App\Models\Tempo;
use Filament\Widgets\Widget; // <-- 1. Ganti menjadi Widget dasar

class TempoDueToday extends Widget
{
    // 2. Definisikan file Blade yang akan kita gunakan
    protected static string $view = 'filament.widgets.tempo-due-today-card';

    // 3. Tambahkan heading untuk judul widget
    protected static ?string $heading = 'Jatuh Tempo Hari Ini';

    protected int|string|array $columnSpan = 'full';

    // 4. Method untuk mengambil dan mengirim data ke view
    protected function getViewData(): array
    {
        $tempos = Tempo::query()
            ->with('car')
            ->whereDate('jatuh_tempo', today())
            ->get();

        return [
            'tempos' => $tempos, // Kirim data dengan nama 'tempos'
        ];
    }
}