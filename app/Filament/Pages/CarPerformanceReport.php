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
    protected static ?string $navigationGroup = 'Laporan & Accounting';
    protected static ?int $navigationSort = 4;
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
            ->with([
                'carModel.brand',
                'bookings' => function ($query) use ($startDate, $endDate) {
                    $query->with('customer')
                        ->where('status', '!=', 'batal')
                        ->where(function ($q) use ($startDate, $endDate) {
                            $q->where('tanggal_keluar', '<=', $endDate)
                                ->where('tanggal_kembali', '>=', $startDate);
                        });
                }
            ])
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
            $totalCostInMonth = 0;
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

                // $revenueInMonth = 0;
                // if ($booking->total_hari > 0) {
                //     $dailyRate = $booking->estimasi_biaya / $booking->total_hari;
                //     $revenueInMonth = $dailyRate * $daysInMonth;
                //     $totalRevenueInMonth += $revenueInMonth;
                // }
                $costInMonth = 0;
                if ($booking->total_hari > 0) {

                    // Pendapatan prorata
                    $dailyRate = $booking->estimasi_biaya / $booking->total_hari;
                    $revenueInMonth = $dailyRate * $daysInMonth;

                    $totalRevenueInMonth += $revenueInMonth;

                    // Harga pokok prorata
                    $costInMonth = ($car->harga_pokok ?? 0) * $daysInMonth;

                    $totalCostInMonth += $costInMonth;
                }

                $bookingsInMonth[] = [
                    'id' => $booking->id,
                    'customer' => $booking->customer->nama,
                    'start' => $booking->tanggal_keluar,
                    'end' => $booking->tanggal_kembali,
                    'revenue' => $revenueInMonth,
                    'cost' => $costInMonth,
                ];
            }

            $data[] = [
                'car_id' => $car->id,
                'model' => $car->carModel->brand->name . ' ' . $car->carModel->name,
                'nopol' => $car->nopol,
                'days_rented' => $totalDaysInMonth,
                'revenue' => $totalRevenueInMonth,
                'cost' => $totalCostInMonth,
                'bookings' => $bookingsInMonth,
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
        $sheet->mergeCells('A1:E1');
        $sheet->mergeCells('A2:E2');
        $sheet->getStyle('A1:A2')->getFont()->setBold(true);
        $sheet->getStyle('A1:A2')->getAlignment()->setHorizontal('center');

        // Header
        $sheet->fromArray([
            'Mobil',
            'No. Polisi',
            'Total Hari Disewa',
            'Pendapatan',
            'Harga Pokok',
        ], null, 'A4');
        $sheet->getStyle('A4:E4')->getFont()->setBold(true);

        $row = 5;
        $totalDays = 0;
        $totalRevenue = 0;
        $totalCost = 0;

        foreach ($reportData as $data) {
            $sheet->setCellValue("A{$row}", $data['model']);
            $sheet->setCellValue("B{$row}", $data['nopol']);
            $sheet->setCellValue("C{$row}", $data['days_rented']);
            $sheet->setCellValue("D{$row}", $data['revenue']);
            $sheet->setCellValue("E{$row}", $data['cost']);
            $sheet->getStyle("D{$row}")->getNumberFormat()->setFormatCode('"Rp"#,##0');
            $sheet->getStyle("E{$row}")
                ->getNumberFormat()
                ->setFormatCode('"Rp"#,##0');

            $totalDays += $data['days_rented'];
            $totalRevenue += $data['revenue'];
            $totalCost += $data['cost'];
            $row++;
        }

        // Total
        $summaryRow = $row + 1;
        $sheet->setCellValue("A{$summaryRow}", 'TOTAL');
        $sheet->setCellValue("C{$summaryRow}", $totalDays);
        $sheet->setCellValue("D{$summaryRow}", $totalRevenue);
        $sheet->setCellValue("E{$summaryRow}", $totalCost);
        $sheet->getStyle("A{$summaryRow}:E{$summaryRow}")->getFont()->setBold(true);
        $sheet->getStyle("D{$summaryRow}")->getNumberFormat()->setFormatCode('"Rp"#,##0');
        $sheet->getStyle("E{$summaryRow}")->getNumberFormat()->setFormatCode('"Rp"#,##0');

        foreach (range('A', 'E') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        $writer = new Xlsx($spreadsheet);
        $filename = 'laporan_kinerja_mobil_' . str_replace(' ', '_', $reportTitle) . '.xlsx';

        return response()->streamDownload(
            fn() => $writer->save('php://output'),
            $filename
        );
    }

    // -- METHOD BARU UNTUK EKSPOR DETAIL --
    public function exportCarDetail(
        int $carId,
        int $year,
        int $month
    ): StreamedResponse {

        $car = Car::with([
            'bookings.customer',
            'carModel.brand'
        ])->findOrFail($carId);

        $startOfMonth = Carbon::create($year, $month, 1)->startOfDay();

        $endOfMonth = $startOfMonth
            ->copy()
            ->endOfMonth()
            ->endOfDay();

        $bookings = $car->bookings()
            ->with('customer')
            ->where('status', '!=', 'batal')
            ->where(function ($q) use ($startOfMonth, $endOfMonth) {
                $q->where('tanggal_keluar', '<=', $endOfMonth)
                    ->where('tanggal_kembali', '>=', $startOfMonth);
            })
            ->get();

        $spreadsheet = new Spreadsheet();

        $sheet = $spreadsheet->getActiveSheet();

        /**
         * =========================
         * JUDUL
         * =========================
         */
        $sheet->setCellValue(
            'A1',
            "Detail Booking Mobil: {$car->carModel->brand->name} {$car->carModel->name} ({$car->nopol})"
        );

        $sheet->setCellValue(
            'A2',
            "Periode: " . $startOfMonth->locale('id')->isoFormat('MMMM YYYY')
        );

        $sheet->mergeCells('A1:I1');
        $sheet->mergeCells('A2:I2');

        $sheet->getStyle('A1:A2')
            ->getFont()
            ->setBold(true);

        $sheet->getStyle('A1:A2')
            ->getAlignment()
            ->setHorizontal('center');

        /**
         * =========================
         * HEADER
         * =========================
         */
        $headers = [
            'Booking ID',
            'Pelanggan',
            'Tgl Keluar',
            'Tgl Kembali',
            'Total Hari',
            'Hari Dalam Bulan',
            'Pendapatan',
            'Harga Pokok',
            'Profit Bersih',
        ];

        $sheet->fromArray($headers, null, 'A4');

        $sheet->getStyle('A4:I4')
            ->getFont()
            ->setBold(true);

        $sheet->getStyle('A4:I4')
            ->getAlignment()
            ->setHorizontal('center');

        /**
         * =========================
         * DATA
         * =========================
         */
        $row = 5;

        $totalRevenue = 0;
        $totalCost = 0;
        $totalProfit = 0;

        foreach ($bookings as $booking) {

            $bookingStart = Carbon::parse($booking->tanggal_keluar)->startOfDay();

            $bookingEnd = Carbon::parse($booking->tanggal_kembali)->endOfDay();

            $periodeMulai = $bookingStart->greaterThan($startOfMonth)
                ? $bookingStart
                : $startOfMonth;

            $periodeSelesai = $bookingEnd->lessThan($endOfMonth)
                ? $bookingEnd
                : $endOfMonth;

            $hariDalamBulan = 0;

            if ($periodeMulai <= $periodeSelesai) {
                $hariDalamBulan = $periodeMulai
                    ->diffInDays($periodeSelesai) + 1;
            }

            /**
             * =========================
             * RESET VARIABLE
             * =========================
             */
            $revenueInMonth = 0;
            $costInMonth = 0;
            $profitInMonth = 0;

            if ($booking->total_hari > 0) {

                /**
                 * =========================
                 * REVENUE
                 * =========================
                 */
                $dailyRate = ($booking->estimasi_biaya ?? 0)
                    / $booking->total_hari;

                $revenueInMonth = $dailyRate * $hariDalamBulan;

                /**
                 * =========================
                 * COST
                 * =========================
                 */
                $dailyCost = (float) ($car->harga_pokok ?? 0);

                $costInMonth = $dailyCost * $hariDalamBulan;

                /**
                 * =========================
                 * PROFIT
                 * =========================
                 */
                $profitInMonth = $revenueInMonth - $costInMonth;
            }

            /**
             * =========================
             * TOTAL
             * =========================
             */
            $totalRevenue += $revenueInMonth;
            $totalCost += $costInMonth;
            $totalProfit += $profitInMonth;

            /**
             * =========================
             * INSERT ROW
             * =========================
             */
            $sheet->fromArray([
                $booking->id,
                $booking->customer->nama ?? '-',
                $booking->tanggal_keluar,
                $booking->tanggal_kembali,
                $booking->total_hari,
                $hariDalamBulan,
                round($revenueInMonth),
                round($costInMonth),
                round($profitInMonth),
            ], null, "A{$row}");

            /**
             * FORMAT RUPIAH
             */
            foreach (['G', 'H', 'I'] as $col) {
                $sheet->getStyle("{$col}{$row}")
                    ->getNumberFormat()
                    ->setFormatCode('"Rp"#,##0');
            }

            $row++;
        }

        /**
         * =========================
         * SUMMARY
         * =========================
         */
        $sheet->setCellValue("F{$row}", 'TOTAL');

        $sheet->setCellValue("G{$row}", round($totalRevenue));

        $sheet->setCellValue("H{$row}", round($totalCost));

        $sheet->setCellValue("I{$row}", round($totalProfit));

        $sheet->getStyle("F{$row}:I{$row}")
            ->getFont()
            ->setBold(true);

        foreach (['G', 'H', 'I'] as $col) {
            $sheet->getStyle("{$col}{$row}")
                ->getNumberFormat()
                ->setFormatCode('"Rp"#,##0');
        }

        /**
         * =========================
         * AUTO SIZE
         * =========================
         */
        foreach (range('A', 'I') as $col) {
            $sheet->getColumnDimension($col)
                ->setAutoSize(true);
        }

        /**
         * =========================
         * DOWNLOAD
         * =========================
         */
        $fileName = "detail_booking_{$car->nopol}_{$year}-{$month}.xlsx";

        return new StreamedResponse(function () use ($spreadsheet) {

            $writer = new Xlsx($spreadsheet);

            $writer->save('php://output');

        }, 200, [
            'Content-Type' =>
                'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',

            'Content-Disposition' =>
                "attachment;filename=\"{$fileName}\"",

            'Cache-Control' => 'max-age=0',
        ]);
    }

    public static function canAccess(): bool
    {
        // Hanya pengguna dengan peran 'admin' yang bisa melihat halaman ini
        return Auth::user()->hasAnyRole(['superadmin', 'admin']);
    }
}

