<?php

namespace App\Filament\Pages;

use App\Models\Car;
use Carbon\Carbon;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Select;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Pages\Page;

class VehicleSchedule extends Page implements HasForms
{
    use InteractsWithForms;

    protected static ?string $navigationIcon = 'heroicon-o-table-cells';
    protected static ?string $title = 'Jadwal Mobil (Excel View)';
    protected static ?string $navigationGroup = 'Transaksi';
    protected static ?string $navigationLabel = 'Jadwal Excel';

    protected static string $view = 'filament.pages.vehicle-schedule';

    public ?array $filterData = [];
    public array $scheduleData = [];

    public function mount(): void
    {
        $this->form->fill([
            'month' => now()->month,
            'year' => now()->year,
        ]);
        $this->loadScheduleData();
    }

    public function form(Form $form): Form
    {
        $years = range(now()->year + 1, now()->year - 2);

        return $form
            ->schema([
                Grid::make(2)->schema([
                    Select::make('month')
                        ->label('Pilih Bulan')
                        ->options(array_reduce(range(1, 12), function ($carry, $month) {
                            $carry[$month] = Carbon::create(null, $month)->isoFormat('MMMM');
                            return $carry;
                        }, []))
                        ->live(),
                    Select::make('year')
                        ->label('Pilih Tahun')
                        ->options(array_combine($years, $years))
                        ->live(),
                ]),
            ])
            ->statePath('filterData');
    }

    public function updatedFilterData(): void
    {
        $this->loadScheduleData();
    }

    protected function loadScheduleData(): void
    {
        $state = $this->form->getState();
        $month = $state['month'];
        $year = $state['year'];

        $startDate = Carbon::create($year, $month, 1)->startOfMonth();
        $endDate = $startDate->copy()->endOfMonth();
        $daysInMonth = $startDate->daysInMonth;

        $cars = Car::with(['carModel.brand', 'bookings' => function ($query) use ($startDate, $endDate) {
            $query->with('customer')->where(function ($q) use ($startDate, $endDate) {
                $q->where('tanggal_keluar', '<=', $endDate)
                  ->where('tanggal_kembali', '>=', $startDate);
            });
        }])->get();

        $data = [
            'cars' => [],
            'daysInMonth' => $daysInMonth,
            'monthName' => $startDate->isoFormat('MMMM YYYY'),
        ];

        foreach ($cars as $car) {
            $dailySchedule = array_fill(1, $daysInMonth, null);

            foreach ($car->bookings as $booking) {
                $bookingStart = Carbon::parse($booking->tanggal_keluar);
                $bookingEnd = Carbon::parse($booking->tanggal_kembali);

                for ($day = 1; $day <= $daysInMonth; $day++) {
                    $currentDay = Carbon::create($year, $month, $day);
                    if ($currentDay->between($bookingStart, $bookingEnd)) {
                        $dailySchedule[$day] = [
                            'customer' => $booking->customer->nama,
                            'status' => $booking->status,
                        ];
                    }
                }
            }

            $data['cars'][] = [
                'id' => $car->id,
                'model' => $car->carModel->name,
                'nopol' => $car->nopol,
                'garasi' => $car->garasi,
                'schedule' => $dailySchedule,
            ];
        }

        $this->scheduleData = $data;
    }
}
