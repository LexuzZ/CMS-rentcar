<?php

namespace App\Filament\Pages;

use App\Models\Booking;
use App\Models\Car;
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
    public string $reportDateString = '';

    public function mount(): void
    {
        $this->form->fill([
            'month' => now()->month,
            'year' => now()->year,
            'nopol_search' => '',
        ]);
        $this->loadReportData();
    }

    public function form(Form $form): Form
    {
        $years = range(now()->year + 1, now()->year - 5);

        return $form
            ->schema([
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

                    TextInput::make('nopol_search')
                        ->label('Cari No. Polisi')
                        ->placeholder('Ketik nopol...')
                        ->live(debounce: 500),
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
        $nopolSearch = $state['nopol_search'] ?? null;

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
                },
            ])
            ->whereHas('bookings', function ($query) use ($startDate, $endDate) {
                $query->where('status', '!=', 'batal')
                    ->where(function ($q) use ($startDate, $endDate) {
                        $q->where('tanggal_keluar', '<=', $endDate)
                            ->where('tanggal_kembali', '>=', $startDate);
                    });
            })
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

                $days = $effectiveStartDate->diffInDays($effectiveEndDate);
                $daysInMonth = $days > 0 ? $days : 1;

                $totalDaysInMonth += $daysInMonth;

                // ✅ Init di LUAR if agar tidak pernah undefined
                $revenueInMonth = 0;
                $costInMonth = 0;

                if ($booking->total_hari > 0) {
                    $dailyRate = $booking->estimasi_biaya / $booking->total_hari;
                    $revenueInMonth = $dailyRate * $daysInMonth;
                    $totalRevenueInMonth += $revenueInMonth;

                    $costInMonth = ($car->harga_pokok ?? 0) * $daysInMonth;
                    $totalCostInMonth += $costInMonth;
                }

                // ✅ days_in_month ikut disimpan
                $bookingsInMonth[] = [
                    'id' => $booking->id,
                    'customer' => $booking->customer->nama,
                    'start' => $booking->tanggal_keluar,
                    'end' => $booking->tanggal_kembali,
                    'days_in_month' => $daysInMonth,  // ← WAJIB ADA
                    'revenue' => $revenueInMonth,
                    'cost' => $costInMonth,  // ← sekarang pasti terisi
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

    // ──────────────────────────────────────────────────────────────────────────
    // EKSPOR RINGKASAN
    // ──────────────────────────────────────────────────────────────────────────
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

        foreach ($reportData as $item) {
            $sheet->setCellValue("A{$row}", $item['model']);
            $sheet->setCellValue("B{$row}", $item['nopol']);
            $sheet->setCellValue("C{$row}", $item['days_rented']);
            $sheet->setCellValue("D{$row}", $item['revenue']);
            $sheet->setCellValue("E{$row}", $item['cost']);
            $sheet->getStyle("D{$row}")->getNumberFormat()->setFormatCode('"Rp"#,##0');
            $sheet->getStyle("E{$row}")->getNumberFormat()->setFormatCode('"Rp"#,##0');

            $totalDays += $item['days_rented'];
            $totalRevenue += $item['revenue'];
            $totalCost += $item['cost'];
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

    // ──────────────────────────────────────────────────────────────────────────
    // EKSPOR DETAIL PER MOBIL
    // ──────────────────────────────────────────────────────────────────────────
    public function exportCarDetail(
        int $carId,
        int $year,
        int $month
    ): StreamedResponse {

        // Ambil data dari reportTableData yang sudah di-load
        $reportData = collect($this->reportTableData)
            ->firstWhere('car_id', $carId);

        if (!$reportData) {
            abort(404, 'Data mobil tidak ditemukan');
        }

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // ── Judul ────────────────────────────────────────────────────────────
        $sheet->setCellValue('A1', 'Detail Harga Pokok Mobil');
        $sheet->setCellValue('A2', "Mobil: {$reportData['model']} ({$reportData['nopol']})");
        $sheet->setCellValue('A3', "Periode: {$this->reportTitle}");

        $sheet->mergeCells('A1:G1');
        $sheet->mergeCells('A2:G2');
        $sheet->mergeCells('A3:G3');

        $sheet->getStyle('A1:A3')->getFont()->setBold(true);
        $sheet->getStyle('A1:A3')->getAlignment()->setHorizontal('center');

        // ── Header ───────────────────────────────────────────────────────────
        $sheet->fromArray([
            'Booking ID',
            'Pelanggan',
            'Tanggal Keluar',
            'Tanggal Kembali',
            'Hari Dalam Bulan',
            'Pendapatan',
            'Harga Pokok',
        ], null, 'A5');
        $sheet->getStyle('A5:G5')->getFont()->setBold(true);

        // ── Data ─────────────────────────────────────────────────────────────
        $row = 6;
        $totalRevenue = 0;
        $totalCost = 0;

        foreach ($reportData['bookings'] as $booking) {
            $sheet->fromArray([
                $booking['id'],
                $booking['customer'],
                $booking['start'],
                $booking['end'],
                $booking['days_in_month'],   // ← sekarang selalu terisi
                $booking['revenue'],
                $booking['cost'],
            ], null, "A{$row}");

            $sheet->getStyle("F{$row}")->getNumberFormat()->setFormatCode('"Rp"#,##0');
            $sheet->getStyle("G{$row}")->getNumberFormat()->setFormatCode('"Rp"#,##0');

            $totalRevenue += $booking['revenue'];
            $totalCost += $booking['cost'];

            $row++;
        }

        // ── Total ────────────────────────────────────────────────────────────
        $sheet->setCellValue("F{$row}", 'TOTAL');
        $sheet->setCellValue("F{$row}", $totalRevenue);  // kolom F = pendapatan
        $sheet->setCellValue("G{$row}", $totalCost);     // kolom G = harga pokok

        // Tulis label TOTAL di kolom E agar tidak menimpa angka
        $sheet->setCellValue("E{$row}", 'TOTAL');
        $sheet->getStyle("E{$row}:G{$row}")->getFont()->setBold(true);
        $sheet->getStyle("F{$row}")->getNumberFormat()->setFormatCode('"Rp"#,##0');
        $sheet->getStyle("G{$row}")->getNumberFormat()->setFormatCode('"Rp"#,##0');

        // ── Auto size ────────────────────────────────────────────────────────
        foreach (range('A', 'G') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        // ── Download ─────────────────────────────────────────────────────────
        $writer = new Xlsx($spreadsheet);
        $filename = 'detail_harga_pokok_' . str_replace(' ', '_', $reportData['nopol']) . '.xlsx';

        return response()->streamDownload(
            fn() => $writer->save('php://output'),
            $filename
        );
    }

    public static function canAccess(): bool
    {
        return Auth::user()->hasAnyRole(['superadmin', 'admin']);
    }
}
