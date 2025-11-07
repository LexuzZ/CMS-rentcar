<?php

namespace App\Filament\Pages;

use App\Filament\Pages\Worksheet\Widgets\AvailableCarsOverview;
use App\Filament\Pages\Worksheet\Widgets\DashboardMonthlySummary;
use App\Filament\Pages\Worksheet\Widgets\InvoiceTable;
use App\Filament\Pages\Worksheet\Widgets\MobilKeluar;
use App\Filament\Pages\Worksheet\Widgets\MobilKembali;
use App\Filament\Pages\Worksheet\Widgets\MonthlyStaffRankingWidget;
use App\Filament\Pages\Worksheet\Widgets\OverdueTasksWidget;
use App\Filament\Pages\Worksheet\Widgets\StaffRankingWidget;
use App\Filament\Pages\Worksheet\Widgets\SummaryWidget;
use App\Filament\Pages\Worksheet\Widgets\TempoDueToday;
use Filament\Pages\Page;

class Worksheet extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-document-text';
    protected static ?string $navigationLabel = 'Lembar Kerja';
    protected ?string $heading = 'Lembar Kerja Staff';

    protected static string $view = 'filament.pages.worksheet';

    protected function getHeaderWidgets(): array
    {
        return [
            AvailableCarsOverview::class,
            DashboardMonthlySummary::class,
            TempoDueToday::class,
            OverdueTasksWidget::class,
            MobilKeluar::class,
            MobilKembali::class,
            StaffRankingWidget::class,
            MonthlyStaffRankingWidget::class,



            // \app\Filament\Pages\Analytic\Widgets\MonthlyRevenueChart::class,

        ];
    }
}
