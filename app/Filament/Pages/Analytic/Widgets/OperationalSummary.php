<?php

namespace App\Filament\Pages\Analytic\Widgets;

use App\Models\Payment;
use App\Models\Pengeluaran;
use App\Models\Booking;
use App\Models\Car;
use Carbon\Carbon;
use Filament\Pages\Page;
use Illuminate\Support\Facades\Auth;

class OperationalSummary extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-chart-bar-square';
    // protected static ?string $navigationGroup = 'Analitik';
    // protected static ?string $title = 'Ringkasan Operasional Bulanan';

    protected static string $view = 'filament.pages.analytic.operational-summary';

    public array $summaryTableData = [];
    public string $reportTitle = '';

    public function mount(): void
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
        $startOfMonth = now()->startOfMonth();
        $endOfMonth = now()->endOfMonth();
        $startOfLastMonth = now()->subMonth()->startOfMonth();
        $endOfLastMonth = now()->subMonth()->endOfMonth();

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

        // --- Utilisasi Armada
        $jumlahMobilSPT = Car::where('garasi', 'SPT')->count();
        $jumlahHariDalamBulan = $startOfMonth->daysInMonth;
        $totalHariTersedia = $jumlahMobilSPT * $jumlahHariDalamBulan;
        $bookingsBulanIni = Booking::whereHas('car', fn ($q) => $q->where('garasi', 'SPT'))
            ->where('tanggal_keluar', '<=', $endOfMonth)
            ->where('tanggal_kembali', '>=', $startOfMonth)
            ->get();
        $totalHariDisewa = 0;
        foreach ($bookingsBulanIni as $b) {
            $actualStart = max(Carbon::parse($b->tanggal_keluar), $startOfMonth);
            $actualEnd = min(Carbon::parse($b->tanggal_kembali), $endOfMonth);
            $totalHariDisewa += $actualStart->diffInDays($actualEnd);
        }
        $utilizationRate = ($totalHariTersedia > 0) ? round(($totalHariDisewa / $totalHariTersedia) * 100) : 0;

        // --- Simpan ke tabel data
        $this->summaryTableData = [
            ['label' => 'Pendapatan Kotor', 'value' => $RevenueMonth, 'change' => $RevenueChange],
            ['label' => 'Profit Garasi', 'value' => $incomeThisMonth, 'change' => $incomeChange],
            ['label' => 'Total Pengeluaran', 'value' => $expenseThisMonth, 'change' => $expenseChange],
            ['label' => 'Laba Bersih', 'value' => $profitThisMonth, 'change' => $profitChange],
            ['label' => 'Total Piutang', 'value' => $receivablesThisMonth, 'change' => $receivablesChange],
            ['label' => 'Utilisasi Armada SPT', 'value' => $utilizationRate . '%', 'change' => null],
        ];

        $this->reportTitle = $startOfMonth->locale('id')->isoFormat('MMMM YYYY');
    }

    public static function canAccess(): bool
    {
        return Auth::user()->hasAnyRole(['superadmin', 'admin']);
    }
}
