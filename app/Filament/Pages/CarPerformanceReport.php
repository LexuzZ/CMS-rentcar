<?php

namespace App\Filament\Pages;

use App\Models\Booking;
use App\Models\Car; // <-- Tambahkan model Car
use Carbon\Carbon;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Pages\Page;

class CarPerformanceReport extends Page implements HasForms
{
    use InteractsWithForms;

    protected static ?string $navigationIcon = 'heroicon-o-document-chart-bar';
    protected static ?string $title = 'Laporan Kinerja Mobil (Prorata)';
    protected static ?string $navigationGroup = 'Laporan';
    protected static ?string $navigationLabel = 'Kinerja Mobil (Prorata)';

    protected static string $view = 'filament.pages.car-performance-report';

    public ?array $filterData = [];
    public array $reportTableData = [];
    public string $reportTitle = '';
    public string $reportDateString = ''; // Properti baru untuk menyimpan Y-m

    public function mount(): void
    {
        $this->form->fill([
            'month' => now()->month,
            'year' => now()->year,
            'nopol_search' => '', // Inisialisasi field pencarian
        ]);
        $this->loadReportData();
    }

    public function form(Form $form): Form
    {
        $years = range(now()->year + 1, now()->year - 5);

        return $form
            ->schema([
                // Mengubah grid menjadi 3 kolom
                Grid::make(3)->schema([
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
                    // Menambahkan input pencarian nopol
                    TextInput::make('nopol_search')
                        ->label('Cari No. Polisi')
                        ->placeholder('Ketik nopol...')
                        ->live(debounce: 500), // Debounce untuk efisiensi
                ]),
            ])
            ->statePath('filterData');
    }

    public function updatedFilterData(): void
    {
        $this->loadReportData();
    }

    protected function loadReportData(): void
    {
        $state = $this->form->getState();
        $month = $state['month'];
        $year = $state['year'];
        $nopolSearch = $state['nopol_search'] ?? null; // Ambil nilai pencarian

        $startDate = Carbon::create($year, $month, 1)->startOfDay();
        $endDate = $startDate->copy()->endOfMonth()->startOfDay();

        $carsQuery = Car::query()
            ->with(['carModel.brand', 'bookings' => function ($query) use ($startDate, $endDate) {
                $query->with('customer')
                      ->where('status', '!=', 'batal')
                      ->where(function ($q) use ($startDate, $endDate) {
                          $q->where('tanggal_keluar', '<=', $endDate)
                            ->where('tanggal_kembali', '>=', $startDate);
                      });
            }])
            ->whereHas('bookings', function ($query) use ($startDate, $endDate) {
                $query->where('status', '!=', 'batal')
                      ->where(function ($q) use ($startDate, $endDate) {
                          $q->where('tanggal_keluar', '<=', $endDate)
                            ->where('tanggal_kembali', '>=', $startDate);
                      });
            })
            // Menambahkan kondisi pencarian nopol ke query
            ->when($nopolSearch, function ($query) use ($nopolSearch) {
                $query->where('nopol', 'like', "%{$nopolSearch}%");
            });

        $cars = $carsQuery->get();

        $data = [];

        foreach ($cars as $car) {
            $totalDaysInMonth = 0;
            $totalRevenueInMonth = 0;
            $bookingsInMonth = [];

            foreach ($car->bookings as $booking) {
                $bookingStart = Carbon::parse($booking->tanggal_keluar)->startOfDay();
                $bookingEnd = Carbon::parse($booking->tanggal_kembali)->startOfDay();

                $effectiveStartDate = $bookingStart->copy()->max($startDate);
                $effectiveEndDate = $bookingEnd->copy()->min($endDate);

                $days = $effectiveStartDate->diffInDays($effectiveEndDate);
                $daysInMonth = $days >= 0 ? $days + 1 : 1;

                $totalDaysInMonth += $daysInMonth;

                if ($booking->total_hari > 0) {
                    $dailyRate = $booking->estimasi_biaya / $booking->total_hari;
                    $revenueInMonth = $dailyRate * $daysInMonth;
                    $totalRevenueInMonth += $revenueInMonth;
                }

                $bookingsInMonth[] = [
                    'id' => $booking->id,
                    'customer' => $booking->customer->nama,
                    'start' => $booking->tanggal_keluar,
                    'end' => $booking->tanggal_kembali,
                    'revenue' => $revenueInMonth,
                ];
            }

            $data[] = [
                'car_id'    => $car->id, // <-- Menambahkan ID mobil
                'model'     => $car->carModel->brand->name . ' ' . $car->carModel->name,
                'nopol'     => $car->nopol,
                'days_rented' => $totalDaysInMonth,
                'revenue'   => $totalRevenueInMonth,
                'bookings'  => $bookingsInMonth,
            ];
        }

        $this->reportTitle = $startDate->isoFormat('MMMM YYYY');
        $this->reportDateString = $startDate->format('Y-m'); // <-- Menyimpan Y-m untuk URL
        $this->reportTableData = collect($data)->sortByDesc('revenue')->values()->all();
    }
}
