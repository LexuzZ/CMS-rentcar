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
use Illuminate\Support\Facades\Auth;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Symfony\Component\HttpFoundation\StreamedResponse;

class CarPerformanceReport extends Page implements HasForms
{
    use InteractsWithForms;

    protected static ?string $navigationIcon = 'heroicon-o-document-chart-bar';
    protected static ?string $title = 'Rekap Mobil Bulanan';
    protected static ?string $navigationLabel = 'Rekap Mobil Bulanan';

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

                // Logika perhitungan hari non-inklusif
                $days = $effectiveStartDate->diffInDays($effectiveEndDate);
                $daysInMonth = $days > 0 ? $days : 1;

                $totalDaysInMonth += $daysInMonth;

                $revenueInMonth = 0;
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
                'car_id'    => $car->id,
                'model'     => $car->carModel->brand->name . ' ' . $car->carModel->name,
                'nopol'     => $car->nopol,
                'days_rented' => $totalDaysInMonth,
                'revenue'   => $totalRevenueInMonth,
                'bookings'  => $bookingsInMonth,
            ];
        }

        $this->reportTitle = $startDate->locale('id')->isoFormat('MMMM YYYY');
        $this->reportDateString = $startDate->format('Y-m');
        $this->reportTableData = collect($data)->sortByDesc('revenue')->values()->all();
    }

    public function exportReport(): StreamedResponse
    {
        $reportData = $this->reportTableData;
        $reportTitle = $this->reportTitle;

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Judul
        $sheet->setCellValue('A1', 'Laporan Kinerja Mobil');
        $sheet->setCellValue('A2', "Periode: {$reportTitle}");
        $sheet->mergeCells('A1:D1');
        $sheet->mergeCells('A2:D2');
        $sheet->getStyle('A1:A2')->getFont()->setBold(true);
        $sheet->getStyle('A1:A2')->getAlignment()->setHorizontal('center');

        // Header
        $sheet->fromArray(['Mobil', 'No. Polisi', 'Total Hari Disewa', 'Pendapatan (Prorata)'], null, 'A4');
        $sheet->getStyle('A4:D4')->getFont()->setBold(true);

        $row = 5;
        $totalDays = 0;
        $totalRevenue = 0;

        foreach ($reportData as $data) {
            $sheet->setCellValue("A{$row}", $data['model']);
            $sheet->setCellValue("B{$row}", $data['nopol']);
            $sheet->setCellValue("C{$row}", $data['days_rented']);
            $sheet->setCellValue("D{$row}", $data['revenue']);
            $sheet->getStyle("D{$row}")->getNumberFormat()->setFormatCode('"Rp"#,##0');

            $totalDays += $data['days_rented'];
            $totalRevenue += $data['revenue'];
            $row++;
        }

        // Total
        $summaryRow = $row + 1;
        $sheet->setCellValue("A{$summaryRow}", 'TOTAL');
        $sheet->setCellValue("C{$summaryRow}", $totalDays);
        $sheet->setCellValue("D{$summaryRow}", $totalRevenue);
        $sheet->getStyle("A{$summaryRow}:D{$summaryRow}")->getFont()->setBold(true);
        $sheet->getStyle("D{$summaryRow}")->getNumberFormat()->setFormatCode('"Rp"#,##0');

        foreach (range('A', 'D') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        $writer = new Xlsx($spreadsheet);
        $filename = 'laporan_kinerja_mobil_' . str_replace(' ', '_', $reportTitle) . '.xlsx';

        return response()->streamDownload(
            fn () => $writer->save('php://output'),
            $filename
        );
    }

    // -- METHOD BARU UNTUK EKSPOR DETAIL --
    public function exportCarDetail(int $carId, int $year, int $month): StreamedResponse
    {
        $car = Car::with(['bookings.customer'])->findOrFail($carId);

        $startOfMonth = Carbon::create($year, $month, 1)->startOfDay();
        $endOfMonth   = $startOfMonth->copy()->endOfMonth()->endOfDay();

        $bookings = $car->bookings()
            ->with('customer')
            ->where('status', '!=', 'batal')
            ->where(function ($q) use ($startOfMonth, $endOfMonth) {
                $q->where('tanggal_keluar', '<=', $endOfMonth)
                  ->where('tanggal_kembali', '>=', $startOfMonth);
            })
            ->get();

        $fileName = "detail_booking_{$car->nopol}_{$year}-{$month}.csv";

        $headers = [
            "Content-type" => "text/csv",
            "Content-Disposition" => "attachment; filename=$fileName",
            "Pragma" => "no-cache",
            "Cache-Control" => "must-revalidate, post-check=0, pre-check=0",
            "Expires" => "0"
        ];

        $columns = [
            'Booking ID',
            'Pelanggan',
            'Tgl Keluar',
            'Tgl Kembali',
            'Total Hari',
            'Hari Dalam Bulan Ini',
            'Pendapatan Bulan Ini (Rp)',
        ];

        $callback = function () use ($bookings, $columns, $startOfMonth, $endOfMonth) {
            $file = fopen('php://output', 'w');

            // judul
            fputcsv($file, ["Detail Booking Mobil"]);
            fputcsv($file, ["Periode: {$startOfMonth->isoFormat('MMMM YYYY')}"]);
            fputcsv($file, []); // baris kosong

            // header
            fputcsv($file, $columns);

            $totalRevenue = 0;
            foreach ($bookings as $booking) {
                $bookingStart = Carbon::parse($booking->tanggal_keluar)->startOfDay();
                $bookingEnd   = Carbon::parse($booking->tanggal_kembali)->endOfDay();

                $periodeMulai   = $bookingStart->greaterThan($startOfMonth) ? $bookingStart : $startOfMonth;
                $periodeSelesai = $bookingEnd->lessThan($endOfMonth) ? $bookingEnd : $endOfMonth;

                $hariDalamBulan = 0;
                if ($periodeMulai <= $periodeSelesai) {
                    $hariDalamBulan = $periodeMulai->diffInDays($periodeSelesai) + 1;
                }

                $revenueInMonth = 0;
                if ($booking->total_hari > 0 && $booking->estimasi_biaya > 0) {
                    $dailyRate = $booking->estimasi_biaya / $booking->total_hari;
                    $revenueInMonth = $dailyRate * $hariDalamBulan;
                }

                $totalRevenue += $revenueInMonth;

                fputcsv($file, [
                    $booking->id,
                    $booking->customer->nama ?? '-',
                    $booking->tanggal_keluar,
                    $booking->tanggal_kembali,
                    $booking->total_hari,
                    $hariDalamBulan,
                    round($revenueInMonth),
                ]);
            }

            fputcsv($file, []); // spasi
            fputcsv($file, ['', '', '', '', '', 'TOTAL', round($totalRevenue)]);

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    public static function canAccess(): bool
    {
        // Hanya pengguna dengan peran 'admin' yang bisa melihat halaman ini
        return Auth::user()->hasAnyRole(['superadmin', 'admin']);
    }
}

