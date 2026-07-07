<?php

namespace App\Filament\Pages\Analytic\Widgets;

use App\Models\Invoice;
use App\Models\Pengeluaran;
use Filament\Widgets\ChartWidget;
use Filament\Widgets\Concerns\InteractsWithPageFilters;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class MonthlyRevenueChart extends ChartWidget
{
    use InteractsWithPageFilters;

    protected static ?string $heading    = 'Pendapatan & Pengeluaran Bulanan';
    protected static ?string $description = null;
    protected static ?int    $sort        = 2;
    protected int|string|array $columnSpan = 'full';

    public ?string $filter = null;

    // ─────────────────────────────────────────
    //  FILTER TAHUN
    // ─────────────────────────────────────────
    protected function getFilters(): ?array
    {
        $year = now()->year;

        if (is_null($this->filter)) {
            $this->filter = (string) $year;
        }

        return [
            (string) $year       => 'Tahun ' . $year,
            (string) ($year - 1) => 'Tahun ' . ($year - 1),
            (string) ($year - 2) => 'Tahun ' . ($year - 2),
        ];
    }

    // ─────────────────────────────────────────
    //  DATA
    // ─────────────────────────────────────────
    protected function getData(): array
    {
        $year = (int) $this->filter;

        $months = collect(range(1, 12))
            ->map(fn ($m) => Carbon::create()->month($m)->locale('id')->translatedFormat('M'))
            ->toArray();

        // Pendapatan
        $pendapatanRaw = Invoice::select(
                DB::raw('MONTH(tanggal_invoice) as bulan'),
                DB::raw('SUM(total_tagihan) as total')
            )
            ->whereYear('tanggal_invoice', $year)
            ->groupBy(DB::raw('MONTH(tanggal_invoice)'))
            ->pluck('total', 'bulan');

        $pendapatan = collect(range(1, 12))
            ->map(fn ($m) => (int) ($pendapatanRaw[$m] ?? 0))
            ->toArray();

        // Pengeluaran
        $pengeluaranRaw = Pengeluaran::select(
                DB::raw('MONTH(tanggal_pengeluaran) as bulan'),
                DB::raw('SUM(pembayaran) as total')
            )
            ->whereYear('tanggal_pengeluaran', $year)
            ->groupBy(DB::raw('MONTH(tanggal_pengeluaran)'))
            ->pluck('total', 'bulan');

        $pengeluaran = collect(range(1, 12))
            ->map(fn ($m) => (int) ($pengeluaranRaw[$m] ?? 0))
            ->toArray();

        // Laba bersih per bulan (line chart overlay)
        $laba = collect(range(0, 11))
            ->map(fn ($i) => $pendapatan[$i] - $pengeluaran[$i])
            ->toArray();

        // Kalkulasi summary untuk description
        $totalPendapatan = array_sum($pendapatan);
        $totalPengeluaran = array_sum($pengeluaran);
        $totalLaba = $totalPendapatan - $totalPengeluaran;

        static::$description = 'Tahun ' . $year
            . '  ·  Pendapatan: Rp ' . number_format($totalPendapatan, 0, ',', '.')
            . '  ·  Pengeluaran: Rp ' . number_format($totalPengeluaran, 0, ',', '.')
            . '  ·  Laba: Rp ' . number_format($totalLaba, 0, ',', '.');

        return [
            'datasets' => [
                [
                    'label'           => 'Pendapatan',
                    'data'            => $pendapatan,
                    'type'            => 'bar',
                    'backgroundColor' => 'rgba(16,185,129,0.80)',
                    'borderColor'     => '#059669',
                    'borderWidth'     => 1.5,
                    'borderRadius'    => 6,
                    'borderSkipped'   => false,
                    'order'           => 2,
                ],
                [
                    'label'           => 'Pengeluaran',
                    'data'            => $pengeluaran,
                    'type'            => 'bar',
                    'backgroundColor' => 'rgba(244,63,94,0.75)',
                    'borderColor'     => '#e11d48',
                    'borderWidth'     => 1.5,
                    'borderRadius'    => 6,
                    'borderSkipped'   => false,
                    'order'           => 2,
                ],
                [
                    'label'       => 'Laba Bersih',
                    'data'        => $laba,
                    'type'        => 'line',
                    'borderColor' => '#6366f1',
                    'borderWidth' => 2.5,
                    'pointBackgroundColor' => '#6366f1',
                    'pointBorderColor'     => '#fff',
                    'pointBorderWidth'     => 2,
                    'pointRadius'          => 4,
                    'pointHoverRadius'     => 7,
                    'fill'        => false,
                    'tension'     => 0.38,
                    'order'       => 1,
                    'yAxisID'     => 'y',
                ],
            ],
            'labels' => $months,
        ];
    }

    // ─────────────────────────────────────────
    //  OPTIONS
    // ─────────────────────────────────────────
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
                    'display'  => true,
                    'position' => 'top',
                    'align'    => 'end',
                    'labels'   => [
                        'boxWidth'   => 12,
                        'boxHeight'  => 12,
                        'borderRadius' => 3,
                        'useBorderRadius' => true,
                        'padding'    => 16,
                        'font'       => ['size' => 12, 'weight' => '600'],
                        'color'      => '#94a3b8',
                    ],
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
                    'displayColors'   => true,
                    'callbacks'       => [
                        'label' => "function(ctx){
                            const v = ctx.parsed.y;
                            const sign = v < 0 ? '-' : '';
                            return ' ' + ctx.dataset.label + ': ' + sign + 'Rp ' + Math.abs(v).toLocaleString('id-ID');
                        }",
                    ],
                ],
            ],

            'scales' => [
                'x' => [
                    'grid'   => ['display' => false],
                    'ticks'  => [
                        'color' => '#94a3b8',
                        'font'  => ['size' => 11, 'weight' => '600'],
                    ],
                    'border' => ['display' => false],
                    'stacked' => false,
                ],
                'y' => [
                    'grid'  => [
                        'color'      => 'rgba(148,163,184,0.08)',
                        'drawBorder' => false,
                    ],
                    'ticks' => [
                        'color'    => '#94a3b8',
                        'font'     => ['size' => 10],
                        'callback' => "function(v){
                            if(v >= 1000000000) return 'Rp '+(v/1000000000).toFixed(1)+'M';
                            if(v >= 1000000)    return 'Rp '+(v/1000000).toFixed(0)+'jt';
                            if(v >= 1000)       return 'Rp '+(v/1000).toFixed(0)+'rb';
                            if(v <= -1000000)   return '-Rp '+(-v/1000000).toFixed(0)+'jt';
                            return 'Rp '+v;
                        }",
                        'maxTicksLimit' => 7,
                    ],
                    'border' => ['display' => false],
                ],
            ],
        ];
    }

    // ─────────────────────────────────────────
    //  DESCRIPTION (dynamic)
    // ─────────────────────────────────────────
    public function getDescription(): ?string
    {
        return static::$description;
    }

    protected function getType(): string
    {
        return 'bar';
    }
}
