<?php

namespace App\Filament\Widgets;

use App\Models\Payment;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\Cache;

class TransactionChart extends ChartWidget
{
    protected static ?string $heading    = 'Transaksi Bulan Ini';
    protected static ?string $description = null;
    protected static ?int    $sort        = 3;
    protected static bool    $isLazy      = true;

    protected int|string|array $columnSpan = [
        'sm' => 'full',
        'md' => 6,
        'lg' => 6,
    ];

    protected function getData(): array
    {
        $year  = now()->year;
        $month = now()->month;
        $daysInMonth = now()->daysInMonth;

        $cacheKey = "transaction_chart_{$year}_{$month}";

        $rows = Cache::remember($cacheKey, 300, function () use ($year, $month) {
            return Payment::query()
                ->selectRaw('DAY(tanggal_pembayaran) as day, SUM(pembayaran) as total')
                ->whereYear('tanggal_pembayaran', $year)
                ->whereMonth('tanggal_pembayaran', $month)
                ->groupBy('day')
                ->orderBy('day')
                ->get()
                ->keyBy('day');
        });

        // Fill every day so the chart has no gaps
        $labels = [];
        $totals = [];
        for ($d = 1; $d <= $daysInMonth; $d++) {
            $labels[] = $d;
            $totals[] = isset($rows[$d]) ? (int) $rows[$d]->total : 0;
        }

        $total      = array_sum($totals);
        $maxDay     = $totals ? array_keys($totals, max($totals))[0] + 1 : '—';
        $activeDays = count(array_filter($totals));

        // Store summary for the description slot
        static::$description = 'Total: Rp ' . number_format($total, 0, ',', '.') .
            '  ·  ' . $activeDays . ' hari aktif';

        return [
            'datasets' => [
                [
                    'label'                => 'Pembayaran (Rp)',
                    'data'                 => $totals,

                    // Gradient fill — resolved in JS via plugin below
                    'fill'                 => true,
                    'tension'              => 0.42,

                    'borderColor'          => '#6366f1',
                    'borderWidth'          => 2.5,

                    // Gradient background handled by getOptions() plugin
                    'backgroundColor'      => 'rgba(99,102,241,0.15)',

                    'pointBackgroundColor' => '#6366f1',
                    'pointBorderColor'     => '#ffffff',
                    'pointBorderWidth'     => 2,
                    'pointRadius'          => 4,
                    'pointHoverRadius'     => 7,
                    'pointHoverBackgroundColor' => '#4f46e5',
                    'pointHoverBorderColor'     => '#ffffff',
                    'pointHoverBorderWidth'     => 2,
                ],
            ],
            'labels' => $labels,
        ];
    }

    protected function getOptions(): array
    {
        return [
            'responsive'          => true,
            'maintainAspectRatio' => false,

            'interaction' => [
                'mode'      => 'index',
                'intersect' => false,
            ],

            'plugins' => [
                'legend' => [
                    'display' => false,
                ],
                'tooltip' => [
                    'enabled'         => true,
                    'backgroundColor' => '#1e1b4b',
                    'titleColor'      => '#a5b4fc',
                    'bodyColor'       => '#e0e7ff',
                    'borderColor'     => '#4338ca',
                    'borderWidth'     => 1,
                    'padding'         => 12,
                    'cornerRadius'    => 10,
                    'displayColors'   => false,
                    'callbacks'       => [
                        // Format tooltip value as Rupiah
                        'label' => "function(ctx){
                            const v = ctx.parsed.y;
                            return ' Rp ' + v.toLocaleString('id-ID');
                        }",
                        'title' => "function(items){
                            return 'Hari ke-' + items[0].label;
                        }",
                    ],
                ],
            ],

            'scales' => [
                'x' => [
                    'grid' => [
                        'display'     => false,
                    ],
                    'ticks' => [
                        'color'    => '#94a3b8',
                        'font'     => ['size' => 11, 'weight' => '500'],
                        'maxTicksLimit' => 10,
                    ],
                    'border' => ['display' => false],
                ],
                'y' => [
                    'position' => 'left',
                    'grid'     => [
                        'color'          => 'rgba(148,163,184,0.08)',
                        'drawBorder'     => false,
                    ],
                    'ticks' => [
                        'color'    => '#94a3b8',
                        'font'     => ['size' => 11],
                        'callback' => "function(v){
                            if(v >= 1000000) return 'Rp '+(v/1000000).toFixed(1)+'jt';
                            if(v >= 1000)    return 'Rp '+(v/1000).toFixed(0)+'rb';
                            return 'Rp '+v;
                        }",
                        'maxTicksLimit' => 6,
                    ],
                    'border' => ['display' => false],
                ],
            ],
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }

    // Extra heading metadata shown below the chart title in Filament
    protected function getHeading(): string
    {
        return now()->translatedFormat('F Y');
    }

    protected function getDescription(): ?string
    {
        // Populated dynamically in getData()
        return static::$description;
    }
}
