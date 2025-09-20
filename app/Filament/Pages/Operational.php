<?php

namespace App\Filament\Pages;

use App\Filament\Pages\Analytic\Widgets\ArusKasTable;
use App\Filament\Pages\Analytic\Widgets\DashboardOverview;
use App\Filament\Pages\Analytic\Widgets\ExpanseCategoryChart;
use App\Filament\Pages\Analytic\Widgets\Piutang;
use App\Filament\Pages\Analytic\Widgets\RecentTransactions;
use App\Filament\Pages\Analytic\Widgets\Revenue;
use App\Filament\Pages\Analytic\Widgets\RevenueCategoryChart;
use App\Filament\Pages\Operasional\Widgets\OperationalSummary;
use Filament\Pages\Page;
use Illuminate\Support\Facades\Auth;

class Operational extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-banknotes';
    protected static ?string $navigationGroup = 'Laporan & Accounting';
    protected static ?string $navigationLabel = 'Ringkasan Operasional';
    protected ?string $heading = 'Ringkasan Operasional Bulan Ini';
    protected static string $view = 'filament.pages.operasional';
    protected function getHeaderWidgets(): array
    {
        return [
            OperationalSummary::class,


        ];
    }
    public static function canAccess(): bool
    {
        // Hanya pengguna dengan peran 'admin' yang bisa melihat halaman ini
        return Auth::user()->hasAnyRole(['superadmin', 'admin']);
    }
}
