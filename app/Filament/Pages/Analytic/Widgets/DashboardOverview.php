<?php

namespace App\Filament\Pages\Analytic\Widgets;

use App\Models\Booking;
use App\Models\Invoice;
use App\Models\Pengeluaran;
use Carbon\Carbon;
use Filament\Widgets\Widget;
use Illuminate\Support\Facades\Auth;

class DashboardOverview extends Widget
{
    protected static string $view = 'filament.widgets.overview';
    protected static ?int $sort = 1;
    protected int|string|array $columnSpan = 'full';

    private function pct(float $current, float $previous): float
    {
        if ($previous == 0) return $current > 0 ? 100 : 0;
        return (($current - $previous) / $previous) * 100;
    }

    private function fmt(float $value): string
    {
        if (abs($value) >= 1_000_000_000) return 'Rp ' . number_format($value / 1_000_000_000, 1, ',', '.') . 'M';
        if (abs($value) >= 1_000_000)     return 'Rp ' . number_format($value / 1_000_000, 1, ',', '.') . 'jt';
        return 'Rp ' . number_format($value, 0, ',', '.');
    }

    protected function getViewData(): array
    {
        $s  = now()->startOfMonth();
        $e  = now()->endOfMonth();
        $sl = now()->subMonth()->startOfMonth();
        $el = now()->subMonth()->endOfMonth();

        // Pendapatan Kotor
        $rev     = Invoice::whereBetween('tanggal_invoice', [$s, $e])->sum('total_paid');
        $revPrev = Invoice::whereBetween('tanggal_invoice', [$sl, $el])->sum('total_paid');

        // Profit Garasi
        $profit     = Booking::whereBetween('tanggal_keluar', [$s, $e])->with('car')->get()
                        ->sum(fn($b) => ($b->car->harga_harian - $b->car->harga_pokok) * $b->total_hari);
        $profitPrev = Booking::whereBetween('tanggal_keluar', [$sl, $el])->with('car')->get()
                        ->sum(fn($b) => ($b->car->harga_harian - $b->car->harga_pokok) * $b->total_hari);

        // Pengeluaran
        $exp     = Pengeluaran::whereBetween('tanggal_pengeluaran', [$s, $e])->sum('pembayaran');
        $expPrev = Pengeluaran::whereBetween('tanggal_pengeluaran', [$sl, $el])->sum('pembayaran');

        // Piutang
        $rcv     = Invoice::whereBetween('tanggal_invoice', [$s, $e])->sum('sisa_pembayaran');
        $rcvPrev = Invoice::whereBetween('tanggal_invoice', [$sl, $el])->sum('sisa_pembayaran');

        // Laba Bersih
        $net     = $rev - $exp;
        $netPrev = $revPrev - $expPrev;

        // Utilisasi
        $jumlahMobil    = \App\Models\Car::where('garasi', 'SPT')->count();
        $daysInMonth    = $s->daysInMonth;
        $totalTersedia  = $jumlahMobil * $daysInMonth;
        $totalDisewa    = \App\Models\Booking::whereHas('car', fn($q) => $q->where('garasi', 'SPT'))
            ->where('tanggal_keluar', '<=', $e)->where('tanggal_kembali', '>=', $s)->get()
            ->sum(function ($b) use ($s, $e) {
                $start = max(Carbon::parse($b->tanggal_keluar), $s);
                $end   = min(Carbon::parse($b->tanggal_kembali), $e);
                return max(0, $start->diffInDays($end));
            });
        $utilRate = $totalTersedia > 0 ? ($totalDisewa / $totalTersedia) * 100 : 0;

        $bulan = now()->locale('id')->isoFormat('MMMM YYYY');

        return [
            'bulan' => $bulan,
            'stats' => [
                [
                    'label'    => 'Pendapatan Kotor',
                    'value'    => $this->fmt($rev),
                    'rawValue' => $rev,
                    'pct'      => $this->pct($rev, $revPrev),
                    'good'     => 'up',   // naik = bagus
                    'icon'     => 'M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z',
                    'color'    => 'blue',
                ],
                [
                    'label'    => 'Profit Garasi',
                    'value'    => $this->fmt($profit),
                    'rawValue' => $profit,
                    'pct'      => $this->pct($profit, $profitPrev),
                    'good'     => 'up',
                    'icon'     => 'M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z',
                    'color'    => 'teal',
                ],
                [
                    'label'    => 'Laba Bersih',
                    'value'    => $this->fmt($net),
                    'rawValue' => $net,
                    'pct'      => $this->pct($net, $netPrev),
                    'good'     => 'up',
                    'icon'     => 'M13 7h8m0 0v8m0-8l-8 8-4-4-6 6',
                    'color'    => 'green',
                ],
                [
                    'label'    => 'Total Pengeluaran',
                    'value'    => $this->fmt($exp),
                    'rawValue' => $exp,
                    'pct'      => $this->pct($exp, $expPrev),
                    'good'     => 'down', // turun = bagus
                    'icon'     => 'M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z',
                    'color'    => 'rose',
                ],
                [
                    'label'    => 'Total Piutang',
                    'value'    => $this->fmt($rcv),
                    'rawValue' => $rcv,
                    'pct'      => $this->pct($rcv, $rcvPrev),
                    'good'     => 'down',
                    'icon'     => 'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01',
                    'color'    => 'amber',
                ],
                [
                    'label'       => 'Utilisasi Armada SPT',
                    'value'       => number_format($utilRate, 0) . '%',
                    'rawValue'    => $utilRate,
                    'description' => "$totalDisewa dari $totalTersedia hari",
                    'pct'         => null,
                    'good'        => 'up',
                    'icon'        => 'M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z',
                    'color'       => 'violet',
                ],
            ],
        ];
    }

    public static function canView(): bool
    {
        return Auth::user()?->hasAnyRole(['superadmin', 'admin']);
    }
}
