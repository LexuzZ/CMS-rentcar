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
    protected int|string|array $columnSpan = 'full';
    protected static ?int $sort = 2;

    public ?string $dateFilter = null;

    public function mount(): void
    {
        $this->dateFilter = now()->format('Y-m-d');
        $this->form->fill(['dateFilter' => $this->dateFilter]);
    }

    public function form(Form $form): Form
    {
        return $form->schema([
            DatePicker::make('dateFilter')
                ->label('Filter Tanggal')
                ->native(false)
                ->displayFormat('d F Y')
                ->closeOnDateSelection()
                ->live()
                ->afterStateUpdated(fn ($state) => $this->dateFilter = $state),
        ]);
    }

    protected function getViewData(): array
    {
        $date = $this->dateFilter;

        $drivers = Driver::withCount([

            // Hitung driver pengantaran HANYA jika status = disewa
            'antar as antar_count' => function ($q) use ($date) {
                $q->whereDate('tanggal_keluar', $date)
                  ->where('status', 'disewa');
            },

            // Hitung driver pengembalian HANYA jika status = selesai
            'jemput as jemput_count' => function ($q) use ($date) {
                $q->whereDate('tanggal_kembali', $date)
                  ->where('status', 'selesai');
            },

        ])->get();

        $stats = $drivers->map(function ($driver) {
            $antar = $driver->antar_count;
            $jemput = $driver->jemput_count;

            return [
                'staff_name' => $driver->nama ?? 'Tanpa Nama',
                'penyerahan' => $antar,
                'pengembalian' => $jemput,
                'total' => $antar + $jemput,
            ];
        })
        ->filter(fn ($stat) => $stat['total'] > 0)
        ->sortByDesc('total')
        ->take(10);

        return [
            'stats' => $stats,
            'dateForHumans' => Carbon::parse($date)
                ->locale('id')
                ->translatedFormat('d F Y'),
        ];
    }
}
