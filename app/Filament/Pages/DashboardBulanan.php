<?php

namespace App\Filament\Pages;

use App\Filament\Pages\Overview\Widgets\BiayaInvestorPerGarasiChart;
use App\Filament\Pages\Overview\Widgets\DashboardStatsOverview;
use App\Filament\Pages\Overview\Widgets\MobilPalingSepiChart;
use App\Filament\Pages\Overview\Widgets\MobilTerlarisChart;
use Filament\Pages\Page;
use Illuminate\Support\Facades\Auth;

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
            BiayaInvestorPerGarasiChart::class
        ];
    }
    public static function canAccess(): bool
    {
        // Hanya pengguna dengan peran 'admin' yang bisa melihat halaman ini
        return Auth::user()->hasAnyRole(['superadmin', 'admin']);
    }
}
