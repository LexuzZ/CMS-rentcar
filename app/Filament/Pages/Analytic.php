<?php

namespace App\Filament\Pages;


use Filament\Pages\Page;
use Illuminate\Support\Facades\Auth;

class Analytic extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    protected static string $view = 'filament.pages.analytic';
    protected function getHeaderWidgets(): array
    {
        return [
            \App\Filament\Pages\Analytic\Widgets\DashboardOverview::class,
            \App\Filament\Pages\Analytic\Widgets\MonthlyRevenueChart::class,
            // \app\Filament\Pages\Analytic\Widgets\MonthlyRevenueChart::class,

        ];
    }
    public static function canAccess(): bool
    {
        // Hanya pengguna dengan peran 'admin' yang bisa melihat halaman ini
        return Auth::user()->hasAnyRole(['superadmin', 'admin']);
    }
}
