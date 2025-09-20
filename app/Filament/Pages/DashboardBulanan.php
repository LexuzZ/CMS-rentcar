<?php

namespace App\Filament\Pages;

use App\Filament\Pages\Overview\Widgets\DashboardStatsOverview;
use App\Filament\Pages\Overview\Widgets\MobilPalingSepiChart;
use App\Filament\Pages\Overview\Widgets\MobilTerlarisChart;
use Filament\Pages\Page;

class DashboardBulanan extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-chart-pie';
    protected static string $view = 'filament.pages.dashboard-bulanan';
    protected static ?string $navigationLabel = 'Ringkasan Operasional';
    protected ?string $heading = 'Ringkasan Operasional Bulan Ini';

    // Daftarkan semua widget Anda di sini
    protected function getHeaderWidgets(): array
    {
        return [
            DashboardStatsOverview::class,
            MobilTerlarisChart::class,
            MobilPalingSepiChart::class,
        ];
    }
}
