<?php

namespace App\Filament\Pages\Analytic\Widgets;
use App\Models\Invoice;
use App\Models\Payment;
use App\Models\Penalty;
use Carbon\Carbon;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\DB;

class RevenueCategoryChart extends ChartWidget
{
    protected static ?string $heading = 'Komposisi Pendapatan per Kategori';
    protected static ?int $sort = 4; // Tampil setelah pengeluaran

    public ?string $filter = 'this_month'; // Properti untuk menyimpan filter aktif

    /**
     * Menggunakan metode filter dasar.
     */
    protected function getFilters(): ?array
    {
        return [
            'this_month' => 'Bulan Ini',
            'last_month' => 'Bulan Lalu',
            'this_year' => 'Tahun Ini',
        ];
    }

    protected function getData(): array
    {
        $period = $this->filter;

        // Tentukan rentang tanggal berdasarkan filter yang dipilih
        [$startDate, $endDate] = match ($period) {
            'last_month' => [Carbon::now()->subMonth()->startOfMonth(), Carbon::now()->subMonth()->endOfMonth()],
            'this_year' => [Carbon::now()->startOfYear(), Carbon::now()->endOfYear()],
            default => [Carbon::now()->startOfMonth(), Carbon::now()->endOfMonth()], // 'this_month'
        };

        // === Ambil data sesuai kategori DENGAN FILTER TANGGAL ===
        $totalRevenueMonth = Payment::whereBetween('tanggal_pembayaran', [$startDate, $endDate])
            ->where('status', 'lunas')
            ->get()
            ->sum(function ($payment) {
                // Pastikan relasi ada sebelum diakses untuk menghindari error
                if ($payment->invoice?->booking?->car) {
                    $totalDays = $payment->invoice->booking->total_hari;
                    $hargaPokokTotal = $payment->invoice->booking->car->harga_pokok * $totalDays;
                    $hargaHarianTotal = $payment->invoice->booking->car->harga_harian * $totalDays;
                    return $hargaHarianTotal - $hargaPokokTotal; // profit marketing
                }
                return 0;
            });

        $ongkir = Invoice::whereBetween('created_at', [$startDate, $endDate])->sum('pickup_dropOff');

        // Untuk penalty, asumsikan tanggal dibuatnya penalty relevan dengan periode
        $klaimBbm = Penalty::where('klaim', 'bbm')->whereBetween('created_at', [$startDate, $endDate])->sum('amount');
        $klaimOvertime = Penalty::where('klaim', 'overtime')->whereBetween('created_at', [$startDate, $endDate])->sum('amount');
        $klaimBaret = Penalty::where('klaim', 'baret')->whereBetween('created_at', [$startDate, $endDate])->sum('amount');
        $klaimOverland = Penalty::where('klaim', 'overland')->whereBetween('created_at', [$startDate, $endDate])->sum('amount');
        $klaimWasher = Penalty::where('klaim', 'washer')->whereBetween('created_at', [$startDate, $endDate])->sum('amount');

        $RevenueMonth = Payment::where('status', 'lunas')
            ->whereBetween('tanggal_pembayaran', [$startDate, $endDate])
            // ->join('invoices', 'payments.invoice_id', '=', 'invoices.id')
            ->sum('pembayaran');
        $PiutangMonth = Payment::where('status', 'belum_lunas')
            ->whereBetween('tanggal_pembayaran', [$startDate, $endDate])
            // ->join('invoices', 'payments.invoice_id', '=', 'invoices.id')
            ->sum('pembayaran');

        // === Mapping ke chart (tidak ada perubahan di sini) ===
        $labels = [
            'Profit Marketing', 'Ongkir', 'Klaim BBM', 'Klaim Baret',
            'Klaim Overtime', 'Klaim Overland', 'Klaim Cuci Mobil', 'Pendapatan Kotor', 'Piutang',
        ];

        $data = [
            $totalRevenueMonth, $ongkir, $klaimBbm, $klaimBaret,
            $klaimOvertime, $klaimOverland, $klaimWasher, $RevenueMonth, $PiutangMonth
        ];

        // Saring data dan label yang nilainya 0 agar chart lebih bersih
        $filteredData = collect($data)->zip($labels)->filter(function ($pair) {
            return $pair[0] > 0; // Hanya ambil data yang nilainya lebih dari 0
        });

        $finalLabels = $filteredData->pluck(1)->toArray();
        $finalData = $filteredData->pluck(0)->toArray();

        $colorPalette = [
            '#8B5CF6', '#38BDF8', '#14B8A6', '#F59E0B', '#F97316',
            '#64748B', '#6366F1', '#10B981' , '#F43F5E',
        ];

        // $colors = collect($finalLabels)->map(function ($label, $index) use ($colorPalette) {
        //     return $colorPalette[$index % count($colorPalette)];
        // })->toArray();

        return [
            'datasets' => [
                [
                    'label' => 'Pendapatan',
                    'data' => $finalData,
                    'backgroundColor' => $colorPalette,
                ],
            ],
            'labels' => $finalLabels,
        ];
    }

    protected function getType(): string
    {
        return 'doughnut';
    }
}
