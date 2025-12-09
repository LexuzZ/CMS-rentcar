<?php

namespace App\Filament\Pages\Worksheet\Widgets;

use App\Models\Driver;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Widgets\Widget;
use Illuminate\Support\Carbon;

class StaffRankingWidget extends Widget implements HasForms
{
    use InteractsWithForms;

    protected static string $view = 'filament.widgets.staff-ranking-widget';

    // Mengatur lebar widget agar full width (opsional)
    protected int | string | array $columnSpan = 'full';

    // Urutan widget di dashboard
    protected static ?int $sort = 2;

    // Property untuk menampung filter tanggal
    public ?string $dateFilter = null;

    public function mount(): void
    {
        // Default filter ke hari ini
        $this->dateFilter = now()->format('Y-m-d');

        // Inisialisasi form
        $this->form->fill(['dateFilter' => $this->dateFilter]);
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                DatePicker::make('dateFilter')
                    ->label('Filter Tanggal')
                    ->native(false)
                    ->displayFormat('d F Y')
                    ->closeOnDateSelection()
                    ->live() // Agar data langsung update saat tanggal dipilih
                    ->afterStateUpdated(function ($state) {
                        $this->dateFilter = $state;
                    }),
            ]);
    }

    protected function getViewData(): array
    {
        $date = $this->dateFilter;

        // Ambil semua driver beserta hitungan tugasnya pada tanggal yang dipilih
        $drivers = Driver::withCount([
            // Hitung tugas Antar berdasarkan 'tanggal_keluar'
            'bookingsAntar' => function ($query) use ($date) {
                if ($date) {
                    $query->whereDate('tanggal_keluar', $date);
                }
            },
            // Hitung tugas Jemput berdasarkan 'tanggal_kembali'
            'bookingsJemput' => function ($query) use ($date) {
                if ($date) {
                    $query->whereDate('tanggal_kembali', $date);
                }
            }
        ])->get();

        // Transformasi data untuk menghitung total dan format array
        $stats = $drivers->map(function ($driver) {
            $antar = $driver->bookings_antar_count ?? 0;
            $jemput = $driver->bookings_jemput_count ?? 0;

            return [
                'staff_name' => $driver->name, // Pastikan kolom 'name' ada di tabel drivers
                'penyerahan' => $antar,
                'pengembalian' => $jemput,
                'total' => $antar + $jemput,
            ];
        })
        // Filter: hanya ambil yang punya aktivitas (total > 0)
        ->filter(fn ($stat) => $stat['total'] > 0)
        // Urutkan dari total terbanyak (Descending)
        ->sortByDesc('total')
        // Ambil top 10 saja
        ->take(10);

        return [
            'stats' => $stats,
            'dateForHumans' => $date ? Carbon::parse($date)->translatedFormat('d F Y') : 'Semua Waktu',
        ];
    }
}
