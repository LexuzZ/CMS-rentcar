<?php

namespace App\Filament\Widgets;

use Filament\Widgets\Widget;

class DashboardActionsWidget extends Widget
{
    protected static string $view = 'filament.widgets.dashboard-actions-widget';
    protected int|string|array $columnSpan = 10;
    protected static ?int $sort = 4;
    protected static ?string $heading = 'Akses Cepat';
}
