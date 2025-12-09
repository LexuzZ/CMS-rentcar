<?php

namespace App\Filament\Widgets;

use App\Models\Booking;
use App\Models\Driver;
use Carbon\Carbon;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Widgets\Widget;
use Illuminate\Support\Collection;

class StaffRankingWidget extends Widget implements HasForms
{
    use InteractsWithForms;

    protected static string $view = 'filament.widgets.staff-ranking-widget';
    protected int | string | array $columnSpan = 'full';
    protected static ?int $sort = 6;

    // Variabel ini menampung state form
    public ?array $data = [];

    public function mount(): void
    {
        $this->form->fill([
            'selectedDate' => now()->format('Y-m-d'),
        ]);
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                DatePicker::make('selectedDate')
                    ->label('Filter Tanggal')
                    ->default(now())
                    ->maxDate(now())
                    // live() penting agar saat tanggal diganti, widget refresh
                    ->live()
                    ->afterStateUpdated(function () {
                        // Memaksa refresh widget saat tanggal berubah
                        $this->dispatch('refresh-widget');
                    }),
            ])
            ->statePath('data');
    }

    protected function getStats(): Collection
    {
        // PERBAIKAN: Mengambil data langsung dari properti $data, bukan getState()
        $dateString = $this->data['selectedDate'] ?? now()->format('Y-m-d');
        $date = Carbon::parse($dateString);

        // Debugging: Jika ingin cek tanggal yang terpilih (bisa dihapus nanti)
        // dump($date->format('Y-m-d'));

        // 1. Query Penyerahan (Pengantaran)
        $penyerahan = Booking::query()
            ->select('id', 'driver_pengantaran_id', 'tanggal_keluar')
            ->whereNotNull('driver_pengantaran_id')
            ->whereDate('tanggal_keluar', $date)
            ->get();

        // 2. Query Pengembalian
        $pengembalian = Booking::query()
            ->select('id', 'driver_pengembalian_id', 'tanggal_kembali')
            ->whereNotNull('driver_pengembalian_id')
            ->whereDate('tanggal_kembali', $date)
            ->get();

        // Jika tidak ada data sama sekali di kedua query
        if ($penyerahan->isEmpty() && $pengembalian->isEmpty()) {
            return collect([]);
        }

        // Grouping manual collection agar lebih mudah dihitung
        $groupedPenyerahan = $penyerahan->groupBy('driver_pengantaran_id');
        $groupedPengembalian = $pengembalian->groupBy('driver_pengembalian_id');

        // Gabung semua ID driver yang terlibat
        $involvedDriverIds = $groupedPenyerahan->keys()
            ->merge($groupedPengembalian->keys())
            ->unique()
            ->filter();

        if ($involvedDriverIds->isEmpty()) {
            return collect([]);
        }

        // Ambil nama driver
        $drivers = Driver::whereIn('id', $involvedDriverIds)->get()->keyBy('id');

        // Mapping Data untuk Tampilan
        $stats = $involvedDriverIds->map(function ($driverId) use ($drivers, $groupedPenyerahan, $groupedPengembalian) {
            $driver = $drivers->get($driverId);

            // Skip jika driver sudah dihapus tapi ID masih ada di booking
            if (!$driver) return null;

            $countPenyerahan = isset($groupedPenyerahan[$driverId]) ? $groupedPenyerahan[$driverId]->count() : 0;
            $countPengembalian = isset($groupedPengembalian[$driverId]) ? $groupedPengembalian[$driverId]->count() : 0;

            return [
                'staff_name' => $driver->nama, // Pastikan kolom di tabel drivers adalah 'nama'
                'penyerahan' => $countPenyerahan,
                'pengembalian' => $countPengembalian,
                'total' => $countPenyerahan + $countPengembalian,
            ];
        })->filter(); // Hapus yang null

        return $stats->sortByDesc('total')->values();
    }

    public function getViewData(): array
    {
        return [
            'stats' => $this->getStats(),
            'dateForHumans' => Carbon::parse($this->data['selectedDate'] ?? now())
                ->locale('id')
                ->isoFormat('dddd, D MMMM YYYY'),
        ];
    }
}
