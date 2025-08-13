<?php

namespace App\Filament\Widgets;

use Filament\Widgets\Widget;

class AnnouncementWidget extends Widget
{
    protected static string $view = 'filament.widgets.announcement-widget';

    // Menentukan agar widget ini mengambil lebar penuh
    protected int | string | array $columnSpan = 'full';

    // Data pengumuman bisa Anda kelola di sini
    protected function getViewData(): array
    {
        return [
            'announcements' => [
                [
                    'title' => 'SOP Kerja Garasi - Poin Penting',
                    'content' => 'Harap perhatikan tanggung jawab dan aturan utama berikut: 1. Pastikan kondisi mobil selalu siap pakai. 2. Semua update data wajib dilakukan di aplikasi secara real-time. 3. Semua dokumentasi (foto mobil) wajib diunggah ke aplikasi. 4. Tidak diperkenankan meminjamkan mobil di luar sistem aplikasi.',
                    'type' => 'info', // Tipe 'info' (biru) cocok untuk SOP
                ],
                // Anda bisa menambahkan pengumuman lain di sini jika perlu
            ],
        ];
    }
}
