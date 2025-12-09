<?php

namespace App\Filament\Pages\Worksheet\Widgets;

use App\Models\Booking;
use App\Models\Driver;
use Carbon\Carbon;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Select;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Widgets\Widget;
use Illuminate\Support\Collection;

class MonthlyStaffRankingWidget extends Widget implements HasForms
{
    use InteractsWithForms;

    protected static string $view = 'filament.widgets.monthly-staff-ranking-widget';
    protected int|string|array $columnSpan = 'full';
    protected static ?int $sort = 7;

    public ?array $data = [];

    public function mount(): void
    {
        $this->form->fill([
            'selectedMonth' => now()->month,
            'selectedYear' => now()->year,
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
        $years = range(now()->year, now()->year - 5);

        return [
            Grid::make(2)->schema([
                Select::make('selectedMonth')
                    ->label('Pilih Bulan')
                    ->options($this->getMonthList())
                    ->live(),

                Select::make('selectedYear')
                    ->label('Pilih Tahun')
                    ->options(array_combine($years, $years))
                    ->live(),
            ]),
        ];
    }

    private function getMonthList(): array
    {
        return [
            '1' => 'Januari',
            '2' => 'Februari',
            '3' => 'Maret',
            '4' => 'April',
            '5' => 'Mei',
            '6' => 'Juni',
            '7' => 'Juli',
            '8' => 'Agustus',
            '9' => 'September',
            '10' => 'Oktober',
            '11' => 'November',
            '12' => 'Desember',
        ];
    }

    protected function getStats(): Collection
    {
        $state = $this->form->getState() ?? [];
        $month = $state['selectedMonth'] ?? now()->month;
        $year = $state['selectedYear'] ?? now()->year;

        // 1. Penyerahan
        $penyerahan = Booking::whereYear('tanggal_keluar', $year)
            ->whereMonth('tanggal_keluar', $month)
            ->whereNotNull('driver_pengantaran_id')
            ->get()
            ->groupBy('driver_pengantaran_id');

        // 2. Pengembalian
        $pengembalian = Booking::whereYear('tanggal_kembali', $year)
            ->whereMonth('tanggal_kembali', $month)
            ->whereNotNull('driver_pengembalian_id')
            ->get()
            ->groupBy('driver_pengembalian_id');

        // 3. Ambil semua driver yang punya aktivitas
        $involvedDriverIds = $penyerahan->keys()
            ->merge($pengembalian->keys())
            ->unique();

        if ($involvedDriverIds->isEmpty()) {
            return collect();
        }

        // 4. Ambil data driver
        $drivers = Driver::whereIn('id', $involvedDriverIds)->get();

        // 5. Mapping final
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

        return $stats->sortByDesc('total')->values();
    }

    protected function getViewData(): array
    {
        $state = $this->form->getState();

        $dateForHumans = Carbon::createFromDate(
            $state['selectedYear'],
            $state['selectedMonth'],
            1
        )
            ->locale('id')
            ->isoFormat('MMMM YYYY');

        return [
            'stats' => $this->getStats(),
            'dateForHumans' => $dateForHumans,
        ];
    }
}
