<?php

namespace App\Filament\Pages\Analytic\Widgets;

use App\Models\Payment;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\DB;

class MetodePembayaranChart extends ChartWidget
{
    protected static ?string $heading    = null;
    protected static ?int    $sort       = 3;
    public    ?string        $filter     = 'this_month';
    // protected static ?string $maxHeight  = '320px';

    // protected int|string|array $columnSpan = [
    //     'sm' => 'full',
    //     'md' => 2,
    //     'lg' => 2,
    // ];

    protected function getFilters(): ?array
    {
        return [
            'this_month' => 'Bulan Ini',
            'last_month' => 'Bulan Lalu',
            'this_year'  => 'Tahun Ini',
        ];
    }

    public function getHeading(): string
    {
        $label = $this->getFilters()[$this->filter] ?? 'Bulan Ini';
        return 'Metode Pembayaran · ' . $label;
    }

    public function getDescription(): ?string
    {
        $data = $this->getRawData();

        $total = collect($data)->sum('total');
        if ($total === 0) return 'Belum ada transaksi pada periode ini.';

        $top = collect($data)->sortByDesc('total')->first();
        if (!$top) return null;

        $label = match ($top->metode_pembayaran) {
            'tunai'          => 'Tunai',
            'transfer'       => 'Transfer',
            'qris'           => 'QRIS',
            'tunai_transfer' => 'Tunai & Transfer',
            'tunai_qris'     => 'Tunai & QRIS',
            'transfer_qris'  => 'Transfer & QRIS',
            default          => ucfirst($top->metode_pembayaran),
        };

        $pct = round(($top->total / $total) * 100);
        return "Dominan: {$label} ({$pct}% dari total)";
    }

    private function getRawData()
    {
        [$startDate, $endDate] = $this->getDateRange();

        return Payment::query()
            ->select('metode_pembayaran', DB::raw('SUM(pembayaran) as total'))
            ->whereBetween('tanggal_pembayaran', [$startDate, $endDate])
            ->groupBy('metode_pembayaran')
            ->orderByDesc('total')
            ->get();
    }

    private function getDateRange(): array
    {
        return match ($this->filter) {
            'last_month' => [now()->subMonth()->startOfMonth(), now()->subMonth()->endOfMonth()],
            'this_year'  => [now()->startOfYear(), now()->endOfYear()],
            default      => [now()->startOfMonth(), now()->endOfMonth()],
        };
    }

    protected function getData(): array
    {
        $data = $this->getRawData();

        $methods = [
            'tunai'          => ['label' => 'Tunai',            'color' => '#22c55e', 'hover' => '#16a34a'],
            'transfer'       => ['label' => 'Transfer',          'color' => '#3b82f6', 'hover' => '#2563eb'],
            'qris'           => ['label' => 'QRIS',              'color' => '#f59e0b', 'hover' => '#d97706'],
            'tunai_transfer' => ['label' => 'Tunai & Transfer',  'color' => '#8b5cf6', 'hover' => '#7c3aed'],
            'tunai_qris'     => ['label' => 'Tunai & QRIS',      'color' => '#06b6d4', 'hover' => '#0891b2'],
            'transfer_qris'  => ['label' => 'Transfer & QRIS',   'color' => '#ec4899', 'hover' => '#db2777'],
        ];

        // Hanya tampilkan metode yang ada datanya
        $active = $data->filter(fn($row) => ($row->total ?? 0) > 0);

        $labels = $active->map(fn($row) => $methods[$row->metode_pembayaran]['label'] ?? ucfirst($row->metode_pembayaran))->values()->toArray();
        $values = $active->map(fn($row) => (int) $row->total)->values()->toArray();
        $colors = $active->map(fn($row) => $methods[$row->metode_pembayaran]['color'] ?? '#94a3b8')->values()->toArray();
        $hovers = $active->map(fn($row) => $methods[$row->metode_pembayaran]['hover'] ?? '#64748b')->values()->toArray();

        return [
            'datasets' => [
                [
                    'data'                 => $values,
                    'backgroundColor'      => $colors,
                    'hoverBackgroundColor' => $hovers,
                    'borderWidth'          => 3,
                    'borderColor'          => 'transparent',
                    'hoverBorderColor'     => 'transparent',
                    'hoverOffset'          => 8,
                ],
            ],
            'labels' => $labels,
        ];
    }

    protected function getOptions(): array
    {
        return [
            'cutout'  => '68%',
            'plugins' => [
                'legend' => [
                    'position' => 'bottom',
                    'labels'   => [
                        'padding'     => 16,
                        'usePointStyle' => true,
                        'pointStyle'  => 'circle',
                        'font'        => ['size' => 12, 'weight' => '600'],
                        'color'       => '#78716c',
                    ],
                ],
                'tooltip' => [
                    'backgroundColor' => '#1c1917',
                    'borderColor'     => '#292524',
                    'borderWidth'     => 1,
                    'titleColor'      => '#a8a29e',
                    'bodyColor'       => '#fafaf9',
                    'padding'         => 10,
                    'cornerRadius'    => 8,
                    'callbacks'       => [],
                ],
            ],
            'animation' => [
                'animateRotate' => true,
                'duration'      => 600,
            ],
        ];
    }

    protected function getType(): string
    {
        return 'doughnut';
    }
}
