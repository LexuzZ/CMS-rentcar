<?php

namespace App\Filament\Pages;

use App\Filament\Pages\Analytic\Widgets\ArusKasTable;
use App\Filament\Pages\Analytic\Widgets\BiayaInvestorPerGarasiChart;
use App\Filament\Pages\Analytic\Widgets\DashboardOverview;
use App\Filament\Pages\Analytic\Widgets\ExpanseCategoryChart;
use App\Filament\Pages\Analytic\Widgets\MobilPalingSepiChart;
use App\Filament\Pages\Analytic\Widgets\MobilTerlarisChart;
use App\Filament\Pages\Operational\Widgets\OperationalSummary;
use App\Filament\Pages\Analytic\Widgets\Piutang;
use App\Filament\Pages\Analytic\Widgets\RecentTransactions;
use App\Filament\Pages\Analytic\Widgets\Revenue;
use App\Filament\Pages\Analytic\Widgets\RevenueCategoryChart;
use App\Filament\Pages\Operational\Widgets\OperationalSummary as WidgetsOperationalSummary;
use Filament\Pages\Page;
use Illuminate\Support\Facades\Auth;

class Operational extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-banknotes';
    protected static ?string $navigationGroup = 'Laporan & Accounting';
    protected static ?string $navigationLabel = 'Laporan Keuangan';
    protected ?string $heading = 'Ringkasan Keuangan Bulan Ini';
    protected static string $view = 'filament.pages.operational';
    protected function getHeaderWidgets(): array
    {
        return [
            OperationalSummary::class,
            RecentTransactions::class,
            Revenue::class,
            // OperationalSummary::class,

            // MoneyFlowChart::class,

            Piutang::class,
            ArusKasTable::class,

        ];
    }
    public static function canAccess(): bool
    {
        // Hanya pengguna dengan peran 'admin' yang bisa melihat halaman ini
        return Auth::user()->hasAnyRole(['superadmin', 'admin']);
    }
}
