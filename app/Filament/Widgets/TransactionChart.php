<?php

namespace App\Filament\Widgets;

use App\Models\Payment;
use Filament\Widgets\Widget;
use Illuminate\Support\Facades\Cache;

class TransactionChart extends Widget
{
    protected static ?int $sort = 4;
    protected static bool $isLazy = true;

    protected static string $view = 'filament.widgets.transaction-chart';

    protected int|string|array $columnSpan = [
        'sm' => 'full',
        'md' => '10',
        'lg' => '10',
    ];

    protected function getViewData(): array
    {
        $year  = now()->year;
        $month = now()->month;

        $cacheKey = "transaction_chart_{$year}_{$month}";

        $rows = Cache::remember($cacheKey, 300, function () use ($year, $month) {
            return Payment::query()
                ->selectRaw('DAY(tanggal_pembayaran) as day, SUM(pembayaran) as total')
                ->whereYear('tanggal_pembayaran', $year)
                ->whereMonth('tanggal_pembayaran', $month)
                ->groupBy('day')
                ->orderBy('day')
                ->get();
        });

        // Summary stats
        $totalBulanIni = $rows->sum('total');
        $rataHarian    = $rows->count() ? round($totalBulanIni / $rows->count()) : 0;
        $hariTertinggi = $rows->sortByDesc('total')->first();

        return [
            'labels'        => $rows->pluck('day')->map(fn($d) => (string) $d)->values()->toJson(),
            'values'        => $rows->pluck('total')->map(fn($v) => (int) $v)->values()->toJson(),
            'totalBulanIni' => $totalBulanIni,
            'rataHarian'    => $rataHarian,
            'hariTertinggi' => $hariTertinggi,
            'bulan'         => now()->locale('id')->isoFormat('MMMM YYYY'),
        ];
    }
}
