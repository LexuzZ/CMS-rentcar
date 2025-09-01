<?php

namespace App\Filament\Pages;

use App\Models\Car;
use Carbon\Carbon;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput; // <-- 1. Import TextInput
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Pages\Page;
use Illuminate\Support\Facades\Auth;

class VehicleSchedule extends Page implements HasForms
{
    use InteractsWithForms;

    protected static ?string $navigationIcon = 'heroicon-o-table-cells';
    protected static ?string $title = 'Jadwal Unit Mobil';

    protected static ?string $navigationLabel = 'Jadwal Unit';

    protected static string $view = 'filament.pages.vehicle-schedule';

    public ?array $filterData = [];
    public array $scheduleData = [];

    public function mount(): void
    {
        $this->form->fill([
            'month' => now()->month,
            'year' => now()->year,
            'nopol_search' => '', // Inisialisasi field pencarian
        ]);
        $this->loadScheduleData();
    }

    public function form(Form $form): Form
    {
        $years = range(now()->year + 1, now()->year - 2);

        return $form
            ->schema([
                // 2. Mengubah grid menjadi 3 kolom untuk filter baru
                Grid::make(3)->schema([
                    Select::make('month')
                        ->label('Pilih Bulan')
                        ->options(array_reduce(range(1, 12), function ($carry, $month) {
                            $carry[$month] = Carbon::create(null, $month)->locale('id')->isoFormat('MMMM');
                            return $carry;
                        }, []))
                        ->live(),
                    Select::make('year')
                        ->label('Pilih Tahun')
                        ->options(array_combine($years, $years))
                        ->live(),
                    // Menambahkan input pencarian nopol
                    TextInput::make('nopol_search')
                        ->label('Cari No. Polisi')
                        ->placeholder('Ketik nopol...')
                        ->live(debounce: 500), // Debounce agar tidak terlalu sering query
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
        $nopolSearch = $state['nopol_search'] ?? null; // Ambil nilai dari filter nopol

        $startDate = Carbon::create($year, $month, 1)->locale('id')->startOfMonth();
        $endDate = $startDate->copy()->endOfMonth();
        $daysInMonth = $startDate->daysInMonth;

        $carsQuery = Car::query()
            ->with(['carModel.brand', 'bookings' => function ($query) use ($startDate, $endDate) {
                $query->with(['customer', 'invoice'])->where(function ($q) use ($startDate, $endDate) {
                    $q->where('tanggal_keluar', '<=', $endDate)
                      ->where('tanggal_kembali', '>=', $startDate);
                });
            }])
            ->join('car_models', 'cars.car_model_id', '=', 'car_models.id')
            ->where('cars.garasi', 'SPT')
            // 3. Menambahkan kondisi pencarian nopol ke query
            ->when($nopolSearch, function ($query) use ($nopolSearch) {
                $query->where('cars.nopol', 'like', "%{$nopolSearch}%");
            })
            ->orderBy('car_models.name', 'asc')
            ->select('cars.*');

        $cars = $carsQuery->get();

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
                            'display_text' => $booking->invoice ? 'INV #' . $booking->invoice->id : $booking->customer->nama,
                            'status' => $booking->status,
                            'booking_id' => $booking->id,
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
    public static function canAccess(): bool
    {
        // Hanya pengguna dengan peran 'admin' yang bisa melihat halaman ini
        return Auth::user()->hasAnyRole(['superadmin', 'admin']);
    }
}
