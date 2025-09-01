<?php

namespace App\Filament\Pages;

use App\Filament\Pages\Analytic\Widgets\ArusKasTable;
use App\Filament\Pages\Analytic\Widgets\Piutang;
use App\Filament\Pages\Analytic\Widgets\Revenue;
use Filament\Pages\Page;
use Illuminate\Support\Facades\Auth;

class Analytic extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-banknotes';
    protected static ?string $navigationGroup = 'Laporan & Accounting';
    protected static ?string $navigationLabel = 'Laporan Keuangan';
    protected static ?string $modelLabel = 'Laporan Keuangan';
    protected static ?string $pluralModelLabel = 'Laporan Keuangan';
    protected static string $view = 'filament.pages.analytic';
    protected function getHeaderWidgets(): array
    {
        return [
            \App\Filament\Pages\Analytic\Widgets\DashboardOverview::class,
            \App\Filament\Pages\Analytic\Widgets\MonthlyRevenueChart::class,
            Revenue::class,
            Piutang::class,
            ArusKasTable::class,

            // \app\Filament\Pages\Analytic\Widgets\MonthlyRevenueChart::class,

        ];
    }
    public static function canAccess(): bool
    {
        // Hanya pengguna dengan peran 'admin' yang bisa melihat halaman ini
        return Auth::user()->hasAnyRole(['superadmin', 'admin']);
    }
}
