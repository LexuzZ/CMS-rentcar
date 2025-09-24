<?php

namespace App\Filament\Pages\Analytic;

use App\Models\Payment;
use App\Models\Pengeluaran;
use App\Models\Booking;
use App\Models\Car;
use App\Models\Invoice;
use App\Models\Penalty;
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
    protected static ?string $title = 'Ringkasan Operasional Bulanan';

    protected static string $view = 'filament.pages.analytic.operational-summary';

    public ?array $filterData = [];
    public array $summaryTableData = [];
    public array $rincianTableData = [];
    public string $reportTitle = '';

    public function mount(): void
    {
        $this->form->fill([
            'month' => now()->month,
            'year'  => now()->year,
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
        $year  = $state['year'] ?? now()->year;

        $startOfMonth = Carbon::create($year, $month, 1)->startOfMonth();
        $endOfMonth = $startOfMonth->copy()->endOfMonth();
        $startOfLastMonth = $startOfMonth->copy()->subMonth()->startOfMonth();
        $endOfLastMonth = $startOfMonth->copy()->subMonth()->endOfMonth();
        $ongkir = Invoice::whereBetween('created_at', [$startOfMonth, $endOfMonth])->sum('pickup_dropOff');
        $ongkirLastMonth = Invoice::whereBetween('created_at', [$startOfMonth, $endOfMonth])
            ->sum('pickup_dropOff');
        $ongkirChange = $this->calculatePercentageChange($ongkir, $ongkirLastMonth);

        // Untuk penalty, asumsikan tanggal dibuatnya penalty relevan dengan periode
        $klaimBbm = Penalty::where('klaim', 'bbm')->whereBetween('created_at', [$startOfMonth, $endOfMonth])->sum('amount');
        $bbmLastMonth = Penalty::where('klaim', 'bbm')->whereBetween('created_at', [$startOfMonth, $endOfMonth])->sum('amount');
        $bbmChange = $this->calculatePercentageChange($klaimBbm, $bbmLastMonth);
        $klaimOvertime = Penalty::where('klaim', 'overtime')->whereBetween('created_at', [$startOfMonth, $endOfMonth])->sum('amount');
        $klaimBaret = Penalty::where('klaim', 'baret')->whereBetween('created_at', [$startOfMonth, $endOfMonth])->sum('amount');
        $klaimOverland = Penalty::where('klaim', 'overland')->whereBetween('created_at', [$startOfMonth, $endOfMonth])->sum('amount');
        $klaimWasher = Penalty::where('klaim', 'washer')->whereBetween('created_at', [$startOfMonth, $endOfMonth])->sum('amount');

        // --- Revenue (Pendapatan Kotor)
        $RevenueMonth = Payment::where('status', 'lunas')
            ->whereBetween('tanggal_pembayaran', [$startOfMonth, $endOfMonth])
            ->sum('pembayaran');
        $RevenueLastMonth = Payment::where('status', 'lunas')
            ->whereBetween('tanggal_pembayaran', [$startOfLastMonth, $endOfLastMonth])
            ->sum('pembayaran');
        $RevenueChange = $this->calculatePercentageChange($RevenueMonth, $RevenueLastMonth);

        // --- Expense (Pengeluaran)
        $expenseThisMonth = Pengeluaran::whereBetween('tanggal_pengeluaran', [$startOfMonth, $endOfMonth])->sum('pembayaran');
        $expenseLastMonth = Pengeluaran::whereBetween('tanggal_pengeluaran', [$startOfLastMonth, $endOfLastMonth])->sum('pembayaran');
        $expenseChange = $this->calculatePercentageChange($expenseThisMonth, $expenseLastMonth);

        // --- Profit Garasi (Income Bersih dari harga harian - harga pokok)
        $incomeThisMonth = Payment::whereBetween('tanggal_pembayaran', [$startOfMonth, $endOfMonth])
            ->where('status', 'lunas')->get()
            ->sum(fn ($p) => ($p->invoice->booking->car->harga_harian - $p->invoice->booking->car->harga_pokok) * $p->invoice->booking->total_hari);
        $incomeLastMonth = Payment::whereBetween('tanggal_pembayaran', [$startOfLastMonth, $endOfLastMonth])
            ->where('status', 'lunas')->get()
            ->sum(fn ($p) => ($p->invoice->booking->car->harga_harian - $p->invoice->booking->car->harga_pokok) * $p->invoice->booking->total_hari);
        $incomeChange = $this->calculatePercentageChange($incomeThisMonth, $incomeLastMonth);

        // --- Profit Bersih (Income - Expense)
        $profitThisMonth = $incomeThisMonth - $expenseThisMonth;
        $profitLastMonth = $incomeLastMonth - $expenseLastMonth;
        $profitChange = $this->calculatePercentageChange($profitThisMonth, $profitLastMonth);

        // --- Piutang
        $receivablesThisMonth = Payment::where('status', 'belum_lunas')
            ->whereBetween('tanggal_pembayaran', [$startOfMonth, $endOfMonth])->sum('pembayaran');
        $receivablesLastMonth = Payment::where('status', 'belum_lunas')
            ->whereBetween('tanggal_pembayaran', [$startOfLastMonth, $endOfLastMonth])->sum('pembayaran');
        $receivablesChange = $this->calculatePercentageChange($receivablesThisMonth, $receivablesLastMonth);



        $this->rincianTableData = [
            ['label' => 'Ongkir/Pengantaran', 'value' => $ongkir, 'change' => $ongkirChange],
            ['label' => 'Klaim Kerusakan/Baret', 'value' => $klaimBaret, 'change' => $incomeChange],
            ['label' => 'Klaim BBM', 'value' => $klaimBbm, 'change' => $bbmChange],
            ['label' => 'Klaim Terlambat', 'value' => $klaimOvertime, 'change' => $profitChange],
            ['label' => 'Klaim Keluar Pulau', 'value' => $klaimOverland, 'change' => $receivablesChange],
            ['label' => 'Klaim Cuci Mobil', 'value' => $klaimWasher, 'change' => $receivablesChange],

        ];
        $this->summaryTableData = [
            ['label' => 'Pendapatan Kotor', 'value' => $RevenueMonth, 'change' => $RevenueChange],
            ['label' => 'Profit Garasi', 'value' => $incomeThisMonth, 'change' => $incomeChange],
            ['label' => 'Total Pengeluaran', 'value' => $expenseThisMonth, 'change' => $expenseChange],
            ['label' => 'Laba Bersih', 'value' => $profitThisMonth, 'change' => $profitChange],
            ['label' => 'Total Piutang', 'value' => $receivablesThisMonth, 'change' => $receivablesChange],

        ];

        $this->reportTitle = $startOfMonth->locale('id')->isoFormat('MMMM YYYY');
    }

    public static function canAccess(): bool
    {
        return Auth::user()->hasAnyRole(['superadmin', 'admin']);
    }
}
