<?php

namespace App\Filament\Widgets;

use Filament\Widgets\ChartWidget;

class BiayaInvestorPerGarasiChart extends ChartWidget
{
    protected static ?string $heading = 'Chart';

    protected function getData(): array
    {
        return [
            //
        ];
    }

    protected function getType(): string
    {
        return 'bar';
    }
}
