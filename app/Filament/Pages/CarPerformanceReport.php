<?php

namespace App\Filament\Pages;

use App\Models\Booking;
use App\Models\Car;
use Carbon\Carbon;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Select;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Pages\Page;

class CarPerformanceReport extends Page implements HasForms
{
    use InteractsWithForms;

    protected static ?string $navigationIcon = 'heroicon-o-document-chart-bar';
    protected static ?string $title = 'Laporan Mobil Bulanan';

    protected static ?string $navigationLabel = 'Kinerja Mobil (Prorata)';

    protected static string $view = 'filament.pages.car-performance-report';

    public ?array $filterData = [];

    // Properti terpisah untuk data tabel dan judul
    public array $reportTableData = [];
    public string $reportTitle = '';

    public function mount(): void
    {
        $this->form->fill([
            'month' => now()->month,
            'year' => now()->year,
        ]);
        $this->loadReportData();
    }

    public function form(Form $form): Form
    {
        $years = range(now()->year + 1, now()->year - 5);

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
        $this->loadReportData();
    }

    protected function loadReportData(): void
    {
        $state = $this->form->getState();
        $month = $state['month'];
        $year = $state['year'];

        $startDate = Carbon::create($year, $month, 1)->startOfDay();
        $endDate = $startDate->copy()->endOfMonth()->startOfDay();

        // Mengambil data mobil yang relevan
        $cars = Car::with(['carModel.brand', 'bookings' => function ($query) use ($startDate, $endDate) {
            $query->where('status', '!=', 'batal')
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
        ->get();

        $data = [];

        foreach ($cars as $car) {
            $totalDaysInMonth = 0;
            $totalRevenueInMonth = 0;

            foreach ($car->bookings as $booking) {
                $bookingStart = Carbon::parse($booking->tanggal_keluar)->startOfDay();
                $bookingEnd = Carbon::parse($booking->tanggal_kembali)->startOfDay();

                $effectiveStartDate = $bookingStart->copy()->max($startDate);
                $effectiveEndDate = $bookingEnd->copy()->min($endDate);

                $daysInMonth = $effectiveStartDate->diffInDays($effectiveEndDate) ;
                $totalDaysInMonth += $daysInMonth;

                if ($booking->total_hari > 0) {
                    $dailyRate = $booking->estimasi_biaya / $booking->total_hari;
                    $revenueInMonth = $dailyRate * $daysInMonth;
                    $totalRevenueInMonth += $revenueInMonth;
                }
            }

            $data[] = [
                'model' => $car->carModel->brand->name . ' ' . $car->carModel->name,
                'nopol' => $car->nopol,
                'days_rented' => $totalDaysInMonth,
                'revenue' => $totalRevenueInMonth,
            ];
        }

        // Mengisi properti publik dengan data yang sudah diolah
        $this->reportTitle = $startDate->isoFormat('MMMM YYYY');
        $this->reportTableData = collect($data)->sortByDesc('revenue')->values()->all();
    }
}
