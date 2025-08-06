<?php

namespace App\Filament\Pages;

use App\Filament\Pages\Analytic\Widgets\MonthlyRevenueChart;
use Filament\Pages\Page;

class Analytic extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    protected static string $view = 'filament.pages.analytic';
    protected function getHeaderWidgets(): array
    {
        return [
            \App\Filament\Pages\Analytic\Widgets\DashboardOverview::class,
            MonthlyRevenueChart::class
        ];
    }
}
