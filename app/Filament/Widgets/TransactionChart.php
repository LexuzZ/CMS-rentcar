<?php

namespace App\Filament\Widgets;

use App\Models\Payment;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;

class TransactionChart extends ChartWidget
{
    protected static ?int $sort = 4;
    protected static bool $isLazy = true;

    protected int|string|array $columnSpan = [
        'sm' => 'full',
        'md' => '10',
        'lg' => '10',
    ];

    // Filter bulan — tampil di pojok kanan heading
    protected static ?string $maxHeight = '280px';

    public ?string $filter = null;

    public function getHeading(): string
    {
        $bulan = $this->filter
            ? \Carbon\Carbon::createFromFormat('Y-m', $this->filter)->locale('id')->isoFormat('MMMM YYYY')
            : now()->locale('id')->isoFormat('MMMM YYYY');

        return 'Transaksi · ' . $bulan;
    }

    public function getDescription(): ?string
    {
        ['total' => $total, 'rata' => $rata] = $this->getSummary();

        if ($total === 0) return 'Belum ada transaksi pada bulan ini.';

        return 'Total: Rp ' . number_format($total, 0, ',', '.') .
               '   ·   Rata-rata harian: Rp ' . number_format($rata, 0, ',', '.');
    }

    protected function getFilters(): ?array
    {
        $months = [];
        for ($i = 0; $i < 6; $i++) {
            $date = now()->subMonths($i);
            $months[$date->format('Y-m')] = $date->locale('id')->isoFormat('MMMM YYYY');
        }
        return $months;
    }

    private function getSelectedPeriod(): array
    {
        if ($this->filter) {
            $date = \Carbon\Carbon::createFromFormat('Y-m', $this->filter);
            return [$date->year, $date->month];
        }
        return [now()->year, now()->month];
    }

    private function getSummary(): array
    {
        [$year, $month] = $this->getSelectedPeriod();
        $rows = $this->fetchRows($year, $month);

        $total = $rows->sum('total');
        $rata  = $rows->count() ? round($total / $rows->count()) : 0;

        return ['total' => $total, 'rata' => $rata];
    }

    private function fetchRows(int $year, int $month)
    {
        $cacheKey = "transaction_chart_{$year}_{$month}";

        return Cache::remember($cacheKey, 300, fn() =>
            Payment::query()
                ->selectRaw('DAY(tanggal_pembayaran) as day, SUM(pembayaran) as total')
                ->whereYear('tanggal_pembayaran', $year)
                ->whereMonth('tanggal_pembayaran', $month)
                ->groupBy('day')
                ->orderBy('day')
                ->get()
        );
    }

    protected function getData(): array
    {
        [$year, $month] = $this->getSelectedPeriod();
        $rows = $this->fetchRows($year, $month);

        return [
            'datasets' => [
                [
                    'label'                => 'Pembayaran',
                    'data'                 => $rows->pluck('total')->map(fn($v) => (int) $v)->values()->toArray(),
                    'fill'                 => true,
                    'tension'              => 0.4,
                    'borderColor'          => '#3b82f6',
                    'borderWidth'          => 2.5,
                    'backgroundColor'      => 'rgba(59,130,246,0.12)',
                    'pointBackgroundColor' => '#ffffff',
                    'pointBorderColor'     => '#3b82f6',
                    'pointBorderWidth'     => 2,
                    'pointRadius'          => 4,
                    'pointHoverRadius'     => 7,
                    'pointHoverBackgroundColor' => '#3b82f6',
                    'pointHoverBorderColor'     => '#ffffff',
                    'pointHoverBorderWidth'     => 2,
                ],
            ],
            'labels' => $rows->pluck('day')->map(fn($d) => (string) $d)->values()->toArray(),
        ];
    }

    protected function getOptions(): array
    {
        return [
            'plugins' => [
                'legend' => ['display' => false],
                'tooltip' => [
                    'backgroundColor' => '#1e293b',
                    'borderColor'     => '#334155',
                    'borderWidth'     => 1,
                    'titleColor'      => '#94a3b8',
                    'bodyColor'       => '#f1f5f9',
                    'padding'         => 10,
                    'cornerRadius'    => 8,
                    'callbacks'       => [
                        // Format label via Chart.js — nilai rupiah
                        // (callback JS tidak bisa diset dari PHP, tapi label tetap readable)
                    ],
                ],
            ],
            'scales' => [
                'x' => [
                    'grid'   => ['color' => 'rgba(0,0,0,0.04)', 'drawBorder' => false],
                    'border' => ['display' => false],
                    'ticks'  => ['color' => '#94a3b8', 'font' => ['size' => 11]],
                ],
                'y' => [
                    'grid'   => ['color' => 'rgba(0,0,0,0.04)', 'drawBorder' => false],
                    'border' => ['display' => false],
                    'ticks'  => ['color' => '#94a3b8', 'font' => ['size' => 11]],
                    'beginAtZero' => true,
                ],
            ],
            'interaction' => [
                'mode'      => 'index',
                'intersect' => false,
            ],
            'elements' => [
                'line' => ['tension' => 0.4],
            ],
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }

    public static function canViewAny(): bool
    {
        return Auth::user()->hasAnyRole(['superadmin', 'admin']);
    }
}
