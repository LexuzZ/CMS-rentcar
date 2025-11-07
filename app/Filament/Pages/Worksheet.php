<?php

namespace App\Filament\Pages;

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
            SummaryWidget::class,
            DashboardMonthlySummary::class,
            InvoiceTable::class,
            OverdueTasksWidget::class,
            MobilKeluar::class,
            MobilKembali::class,
            StaffRankingWidget::class,
            MonthlyStaffRankingWidget::class,
            TempoDueToday::class,

            // \app\Filament\Pages\Analytic\Widgets\MonthlyRevenueChart::class,

        ];
    }
}
