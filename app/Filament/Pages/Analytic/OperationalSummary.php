<?php

namespace App\Filament\Pages\Analytic;

use App\Filament\Pages\Analytic\Widgets\ArusKasTable;
use App\Filament\Pages\Analytic\Widgets\Piutang;
use App\Filament\Pages\Analytic\Widgets\RecentTransactions;
use App\Filament\Pages\Analytic\Widgets\Revenue;
use App\Models\Payment;
use App\Models\Pengeluaran;
use App\Models\Booking;
use App\Models\Car;
use App\Models\Invoice;
use App\Models\Penalty;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Select;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Pages\Page;
use Illuminate\Support\Facades\Auth;

class OperationalSummary extends Page implements HasForms
{
    use InteractsWithForms;

    protected static ?string $navigationIcon = 'heroicon-o-chart-bar-square';
    protected static ?string $navigationGroup = 'Laporan & Accounting';
    protected static ?string $title = 'Laporan Keuangan';

    protected static string $view = 'filament.pages.analytic.operational-summary';

    public ?array $filterData = [];
    public array $summaryTableData = [];
    public array $rincianTableData = [];
    public array $costTableData = [];
    public array $rincianCostTableData = [];
    public array $costRentTableData = [];
    public string $reportTitle = '';

    public function mount(): void
    {
        $this->form->fill([
            'month' => now()->month,
            'year' => now()->year,
        ]);

        $this->loadSummaryData();
    }

    public function form(Form $form): Form
    {
        $years = range(now()->year + 1, now()->year - 5);

        return $form->schema([
            Grid::make(2)->schema([
                Select::make('month')
                    ->label('Bulan')
                    ->options(array_reduce(range(1, 12), function ($carry, $month) {
                        $carry[$month] = Carbon::create(null, $month)->locale('id')->isoFormat('MMMM');
                        return $carry;
                    }, []))
                    ->required()
                    ->live(),

                Select::make('year')
                    ->label('Tahun')
                    ->options(array_combine($years, $years))
                    ->required()
                    ->live(),
            ]),
        ])->statePath('filterData');
    }

    public function updatedFilterData(): void
    {
        $this->loadSummaryData();
    }

    private function calculatePercentageChange(float $current, float $previous): float
    {
        if ($previous == 0) {
            return $current > 0 ? 100 : 0;
        }
        return (($current - $previous) / $previous) * 100;
    }

    protected function loadSummaryData(): void
    {
        $state = $this->form->getState();
        $month = $state['month'] ?? now()->month;
        $year = $state['year'] ?? now()->year;

        $startOfMonth = Carbon::create($year, $month, 1)->startOfMonth();
        $endOfMonth = $startOfMonth->copy()->endOfMonth();
        $startOfLastMonth = $startOfMonth->copy()->subMonth()->startOfMonth();
        $endOfLastMonth = $startOfMonth->copy()->subMonth()->endOfMonth();

        // Operasional Kantor
        $operational = Pengeluaran::where('nama_pengeluaran', 'operasional')->whereBetween('tanggal_pengeluaran', [$startOfMonth, $endOfMonth])->sum('pembayaran');
        $operationalLastMonth = Pengeluaran::where('nama_pengeluaran', 'operasional')->whereBetween('tanggal_pengeluaran', [$startOfLastMonth, $endOfLastMonth])->sum('pembayaran');
        $operasionalChange = $this->calculatePercentageChange($operational, $operationalLastMonth);
        // Gaji Karyawan
        $gaji = Pengeluaran::where('nama_pengeluaran', 'gaji')->whereBetween('tanggal_pengeluaran', [$startOfMonth, $endOfMonth])->sum('pembayaran');
        $gajiLastMonth = Pengeluaran::where('nama_pengeluaran', 'gaji')->whereBetween('tanggal_pengeluaran', [$startOfLastMonth, $endOfLastMonth])->sum('pembayaran');
        $gajiChange = $this->calculatePercentageChange($gaji, $gajiLastMonth);
        // Rent to Rent
        $rent = Pengeluaran::where('nama_pengeluaran', 'rent')->whereBetween('tanggal_pengeluaran', [$startOfMonth, $endOfMonth])->sum('pembayaran');
        $rentLastMonth = Pengeluaran::where('nama_pengeluaran', 'rent')->whereBetween('tanggal_pengeluaran', [$startOfLastMonth, $endOfLastMonth])->sum('pembayaran');
        $rentChange = $this->calculatePercentageChange($rent, $rentLastMonth);
        // Setoran Investor
        $setoran = Pengeluaran::where('nama_pengeluaran', 'setoran')->whereBetween('tanggal_pengeluaran', [$startOfMonth, $endOfMonth])->sum('pembayaran');
        $setoranLastMonth = Pengeluaran::where('nama_pengeluaran', 'setoran')->whereBetween('tanggal_pengeluaran', [$startOfLastMonth, $endOfLastMonth])->sum('pembayaran');
        $setoranChange = $this->calculatePercentageChange($setoran, $setoranLastMonth);
        // Cicilan Mobil
        $cicilan = Pengeluaran::where('nama_pengeluaran', 'cicilan')->whereBetween('tanggal_pengeluaran', [$startOfMonth, $endOfMonth])->sum('pembayaran');
        $cicilanLastMonth = Pengeluaran::where('nama_pengeluaran', 'cicilan')->whereBetween('tanggal_pengeluaran', [$startOfLastMonth, $endOfLastMonth])->sum('pembayaran');
        $cicilanChange = $this->calculatePercentageChange($cicilan, $cicilanLastMonth);
        // Pajak Mobil
        $pajak = Pengeluaran::where('nama_pengeluaran', 'pajak')->whereBetween('tanggal_pengeluaran', [$startOfMonth, $endOfMonth])->sum('pembayaran');
        $pajakLastMonth = Pengeluaran::where('nama_pengeluaran', 'pajak')->whereBetween('tanggal_pengeluaran', [$startOfLastMonth, $endOfLastMonth])->sum('pembayaran');
        $pajakChange = $this->calculatePercentageChange($pajak, $pajakLastMonth);
        // Perawatan Mobil
        $perawatan = Pengeluaran::where('nama_pengeluaran', 'perawatan')->whereBetween('tanggal_pengeluaran', [$startOfMonth, $endOfMonth])->sum('pembayaran');
        $perawatanLastMonth = Pengeluaran::where('nama_pengeluaran', 'perawatan')->whereBetween('tanggal_pengeluaran', [$startOfLastMonth, $endOfLastMonth])->sum('pembayaran');
        $perawatanChange = $this->calculatePercentageChange($perawatan, $perawatanLastMonth);
        // lainnya
        $lainnya = Pengeluaran::where('nama_pengeluaran', 'lainnya')->whereBetween('tanggal_pengeluaran', [$startOfMonth, $endOfMonth])->sum('pembayaran');
        $lainnyaLastMonth = Pengeluaran::where('nama_pengeluaran', 'lainnya')->whereBetween('tanggal_pengeluaran', [$startOfLastMonth, $endOfLastMonth])->sum('pembayaran');
        $lainnyaChange = $this->calculatePercentageChange($lainnya, $lainnyaLastMonth);

        // --- ONGKIR/PENGANTARAN
        $ongkir = Invoice::whereBetween('created_at', [$startOfMonth, $endOfMonth])->sum('pickup_dropOff');
        $ongkirLastMonth = Invoice::whereBetween('created_at', [$startOfLastMonth, $endOfLastMonth])
            ->sum('pickup_dropOff');
        $ongkirChange = $this->calculatePercentageChange($ongkir, $ongkirLastMonth);

        // --- PENALTY BBM
        $klaimBbm = Penalty::where('klaim', 'bbm')->whereBetween('created_at', [$startOfMonth, $endOfMonth])->sum('amount');
        $bbmLastMonth = Penalty::where('klaim', 'bbm')->whereBetween('created_at', [$startOfLastMonth, $endOfLastMonth])->sum('amount');
        $bbmChange = $this->calculatePercentageChange($klaimBbm, $bbmLastMonth);

        // --- PENALTY OVERTIME
        $klaimOvertime = Penalty::where('klaim', 'overtime')->whereBetween('created_at', [$startOfMonth, $endOfMonth])->sum('amount');
        $overtimeLastMonth = Penalty::where('klaim', 'overtime')->whereBetween('created_at', [$startOfLastMonth, $endOfLastMonth])->sum('amount');
        $overtimeChange = $this->calculatePercentageChange($klaimOvertime, $overtimeLastMonth);

        // --- PENALTY BARET
        $klaimBaret = Penalty::where('klaim', 'baret')->whereBetween('created_at', [$startOfMonth, $endOfMonth])->sum('amount');
        $baretLastMonth = Penalty::where('klaim', 'baret')->whereBetween('created_at', [$startOfLastMonth, $endOfLastMonth])->sum('amount');
        $baretChange = $this->calculatePercentageChange($klaimBaret, $baretLastMonth);

        // --- PENALTY OVERLAND
        $klaimOverland = Penalty::where('klaim', 'overland')->whereBetween('created_at', [$startOfMonth, $endOfMonth])->sum('amount');
        $overlandLastMonth = Penalty::where('klaim', 'overland')->whereBetween('created_at', [$startOfLastMonth, $endOfLastMonth])->sum('amount');
        $overlandChange = $this->calculatePercentageChange($klaimOverland, $overlandLastMonth);

        // --- PENALTY WASHER
        $klaimWasher = Penalty::where('klaim', 'washer')->whereBetween('created_at', [$startOfMonth, $endOfMonth])->sum('amount');
        $washerLastMonth = Penalty::where('klaim', 'washer')->whereBetween('created_at', [$startOfLastMonth, $endOfLastMonth])->sum('amount');
        $washerChange = $this->calculatePercentageChange($klaimWasher, $washerLastMonth);


        // --- Revenue (Pendapatan Kotor)
        $RevenueMonth = Payment::whereBetween('tanggal_pembayaran', [$startOfMonth, $endOfMonth])
            ->where('status', 'lunas')->get()
            ->sum('pembayaran');
        $RevenueLastMonth = Payment::whereBetween('tanggal_pembayaran', [$startOfLastMonth, $endOfLastMonth])
            ->where('status', 'lunas')->get()
            ->sum('pembayaran');
        $RevenueChange = $this->calculatePercentageChange($RevenueMonth, $RevenueLastMonth);

        // --- Expense (Pengeluaran)
        $expenseThisMonth = Pengeluaran::whereBetween('tanggal_pengeluaran', [$startOfMonth, $endOfMonth])->sum('pembayaran');
        $expenseLastMonth = Pengeluaran::whereBetween('tanggal_pengeluaran', [$startOfLastMonth, $endOfLastMonth])->sum('pembayaran');
        $expenseChange = $this->calculatePercentageChange($expenseThisMonth, $expenseLastMonth);

        // --- Profit Garasi (Income Bersih dari harga harian - harga pokok)
        $incomeThisMonth = Payment::whereBetween('tanggal_pembayaran', [$startOfMonth, $endOfMonth])
            ->where('status', 'lunas')->get()
            ->sum(fn($p) => ($p->invoice->booking->car->harga_harian - $p->invoice->booking->car->harga_pokok) * $p->invoice->booking->total_hari);
        $incomeLastMonth = Payment::whereBetween('tanggal_pembayaran', [$startOfLastMonth, $endOfLastMonth])
            ->where('status', 'lunas')->get()
            ->sum(fn($p) => ($p->invoice->booking->car->harga_harian - $p->invoice->booking->car->harga_pokok) * $p->invoice->booking->total_hari);
        $incomeChange = $this->calculatePercentageChange($incomeThisMonth, $incomeLastMonth);
        $pokokThisMonth = Payment::whereBetween('tanggal_pembayaran', [$startOfMonth, $endOfMonth])
            ->where('status', 'lunas')->get()
            ->sum(fn($p) => ($p->invoice->booking->car->harga_pokok) * $p->invoice->booking->total_hari);
        $pokokLastMonth = Payment::whereBetween('tanggal_pembayaran', [$startOfLastMonth, $endOfLastMonth])
            ->where('status', 'lunas')->get()
            ->sum(fn($p) => ($p->invoice->booking->car->harga_pokok) * $p->invoice->booking->total_hari);
        $pokokChange = $this->calculatePercentageChange($pokokThisMonth, $pokokLastMonth);



        $receivablesThisMonth = Payment::where('status', 'belum_lunas')
            ->whereBetween('tanggal_pembayaran', [$startOfMonth, $endOfMonth])->sum('pembayaran');
        $receivablesLastMonth = Payment::where('status', 'belum_lunas')
            ->whereBetween('tanggal_pembayaran', [$startOfLastMonth, $endOfLastMonth])->sum('pembayaran');
        $receivablesChange = $this->calculatePercentageChange($receivablesThisMonth, $receivablesLastMonth);

        $rentMonth = \App\Models\Payment::whereBetween('tanggal_pembayaran', [$startOfMonth, $endOfMonth])
            ->where('status', 'lunas')
            ->with('invoice.booking') // eager load supaya tidak N+1
            ->get()
            ->sum(fn($payment) => $payment->invoice?->booking?->estimasi_biaya ?? 0);

        $rentLastMonth = \App\Models\Payment::whereBetween('tanggal_pembayaran', [$startOfLastMonth, $endOfLastMonth])
            ->where('status', 'lunas')
            ->with('invoice.booking')
            ->get()
            ->sum(fn($payment) => $payment->invoice?->booking?->estimasi_biaya ?? 0);

        $rentChange = $this->calculatePercentageChange($rentMonth, $rentLastMonth);
        $rentPiutangMonth = \App\Models\Payment::whereBetween('tanggal_pembayaran', [$startOfMonth, $endOfMonth])
            ->where('status', 'belum_lunas')
            ->with('invoice.booking') // eager load supaya tidak N+1
            ->get()
            ->sum(fn($payment) => $payment->invoice?->booking?->estimasi_biaya ?? 0);

        $rentPiutangLastMonth = \App\Models\Payment::whereBetween('tanggal_pembayaran', [$startOfLastMonth, $endOfLastMonth])
            ->where('status', 'belum_lunas')
            ->with('invoice.booking')
            ->get()
            ->sum(fn($payment) => $payment->invoice?->booking?->estimasi_biaya ?? 0);

        $rentPiutangChange = $this->calculatePercentageChange($rentPiutangMonth, $rentPiutangLastMonth);
        // --- Profit Bersih (Income - Expense)
        $profitThisMonth = $RevenueMonth - $expenseThisMonth;
        $profitLastMonth = $RevenueLastMonth - $expenseLastMonth;
        $profitChange = $this->calculatePercentageChange($profitThisMonth, $profitLastMonth);

        $this->rincianTableData = [
            ['label' => 'Ongkir/Pengantaran', 'value' => $ongkir, 'change' => $ongkirChange],
            ['label' => 'Klaim Baret/Kerusakan', 'value' => $klaimBaret, 'change' => $baretChange],
            ['label' => 'Klaim BBM', 'value' => $klaimBbm, 'change' => $bbmChange],
            ['label' => 'Klaim Terlambat', 'value' => $klaimOvertime, 'change' => $overtimeChange],
            ['label' => 'Klaim Keluar Pulau', 'value' => $klaimOverland, 'change' => $overlandChange],
            ['label' => 'Klaim Cuci Mobil', 'value' => $klaimWasher, 'change' => $washerChange],
            ['label' => 'Pendapatan Sewa', 'value' => $rentMonth, 'change' => $rentChange],
        ];
        $this->rincianCostTableData = [
            ['label' => 'Operasional Kantor', 'value' => $operational, 'change' => $operasionalChange],
            ['label' => 'Gaji Karyawan', 'value' => $gaji, 'change' => $gajiChange],
            ['label' => 'Cicilan Mobil', 'value' => $cicilan, 'change' => $cicilanChange],
            ['label' => 'Perawatan Mobil', 'value' => $perawatan, 'change' => $perawatanChange],
            ['label' => 'Rent to Rent', 'value' => $rent, 'change' => $rentChange],
            ['label' => 'Pajak', 'value' => $pajak, 'change' => $pajakChange],
            ['label' => 'Setoran Investor', 'value' => $setoran, 'change' => $setoranChange],
            ['label' => 'Lainnya', 'value' => $lainnya, 'change' => $lainnyaChange],


        ];
        $this->summaryTableData = [
            ['label' => 'Lunas', 'value' => $RevenueMonth, 'change' => $RevenueChange],
            // ['label' => 'Pendapatan Pokok', 'value' => $pokokThisMonth, 'change' => $pokokChange],
            ['label' => 'Profit Garasi', 'value' => $incomeThisMonth, 'change' => $incomeChange],
            ['label' => 'Kas Saldo', 'value' => $profitThisMonth, 'change' => $profitChange],


        ];
        $this->costTableData = [
            ['label' => 'Total Kas Pengeluaran', 'value' => $expenseThisMonth, 'change' => $expenseChange],
            ['label' => 'Belum Lunas', 'value' => $receivablesThisMonth, 'change' => $receivablesChange],

        ];
        $this->costRentTableData = [
            ['label' => 'Piutang Sewa', 'value' => $rentPiutangMonth, 'change' => $rentPiutangChange],
        ];

        $this->reportTitle = $startOfMonth->locale('id')->isoFormat('MMMM YYYY');
    }
    public function downloadPdf()
    {
        $this->loadSummaryData(); // refresh data

        $pdf = Pdf::loadView('pdf.operational-summary', [
            'reportTitle' => $this->reportTitle,
            'summaryTableData' => $this->summaryTableData,
            'rincianTableData' => $this->rincianTableData,
            'rincianCostTableData' => $this->rincianCostTableData,
            'costTableData' => $this->costTableData,
            'costRentTableData' => $this->costRentTableData,
        ]);

        return response()->streamDownload(
            fn() => print ($pdf->output()),
            "Laporan_Operasional_{$this->reportTitle}.pdf"
        );
    }

    public static function canAccess(): bool
    {
        return Auth::user()->hasAnyRole(['superadmin', 'admin']);
    }
    public function getFooterWidgets(): array
    {
        return [
            RecentTransactions::class,
            Piutang::class,
            Revenue::class,
            ArusKasTable::class
        ];
    }
}
