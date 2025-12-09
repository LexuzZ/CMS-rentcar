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
        $date = Carbon::parse($this->form->getState()['selectedDate'] ?? now()->format('Y-m-d'));

        // Query penyerahan
        $penyerahan = Booking::query()
            ->whereNotNull('driver_pengantaran_id')
            ->whereDate('tanggal_keluar', $date)
            ->get()
            ->groupBy('driver_pengantaran_id');

        // Query pengembalian
        $pengembalian = Booking::query()
            ->whereNotNull('driver_pengembalian_id')
            ->whereDate('tanggal_kembali', $date)
            ->get()
            ->groupBy('driver_pengembalian_id');

        // Gabung semua ID driver yang terlibat
        $involvedDriverIds = $penyerahan->keys()
            ->merge($pengembalian->keys())
            ->unique()
            ->filter(); // hilangkan null kalau ada

        if ($involvedDriverIds->isEmpty()) {
            return collect();
        }

        // Ambil data driver
        $drivers = Driver::whereIn('id', $involvedDriverIds)->get();

        // Hitung total masing-masing driver
        $stats = $drivers->map(function ($driver) use ($penyerahan, $pengembalian) {
            $penyerahanCount = ($penyerahan[$driver->id] ?? collect())->count();
            $pengembalianCount = ($pengembalian[$driver->id] ?? collect())->count();

            return [
                'staff_name' => $driver->nama,
                'penyerahan' => $penyerahanCount,
                'pengembalian' => $pengembalianCount,
                'total' => $penyerahanCount + $pengembalianCount,
            ];
        });

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
