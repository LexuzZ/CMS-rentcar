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
    protected int|string|array $columnSpan = 'full';
    protected static ?int $sort = 6;

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
            ->schema($this->getFormSchema())
            ->statePath('data');
    }

    protected function getFormSchema(): array
    {
        return [
            DatePicker::make('selectedDate')
                ->label('Pilih Tanggal')
                ->maxDate(now())
                ->live(),
        ];
    }

    protected function getStats(): Collection
    {
        try {
            $date = Carbon::parse($this->form->getState()['selectedDate']);
        } catch (\Exception $e) {
            $date = now();
        }

        // =================
// PENYERAHAN
// =================
        $penyerahan = Booking::whereDate('tanggal_keluar', $date)
            ->whereNotNull('driver_pengantaran_id')
            ->get()
            ->groupBy(fn($b) => intval($b->driver_pengantaran_id));

        // =================
// PENGEMBALIAN
// =================
        $pengembalian = Booking::whereDate('tanggal_kembali', $date)
            ->whereNotNull('driver_pengembalian_id')
            ->get()
            ->groupBy(fn($b) => intval($b->driver_pengembalian_id));


        // Gabungkan semua driver yang terlibat
        $involvedDriverIds = $penyerahan->keys()
            ->merge($pengembalian->keys())
            ->unique();

        if ($involvedDriverIds->isEmpty()) {
            return collect();
        }

        $drivers = Driver::whereIn('id', $involvedDriverIds)->get();

        // Hitung total masing-masing driver
        $stats = $drivers->map(function ($driver) use ($penyerahan, $pengembalian) {
            $penyerahanCount = $penyerahan->get($driver->id, collect())->count();
            $pengembalianCount = $pengembalian->get($driver->id, collect())->count();

            return [
                'staff_name' => $driver->nama,
                'penyerahan' => $penyerahanCount,
                'pengembalian' => $pengembalianCount,
                'total' => $penyerahanCount + $pengembalianCount,
            ];
        });

        // Urutkan
        return $stats->sortByDesc('total')->values();
    }

    public function getViewData(): array
    {
        return [
            'stats' => $this->getStats(),
            'dateForHumans' => Carbon::parse($this->form->getState()['selectedDate'])
                ->locale('id')
                ->isoFormat('D MMMM YYYY'),
        ];
    }
}
