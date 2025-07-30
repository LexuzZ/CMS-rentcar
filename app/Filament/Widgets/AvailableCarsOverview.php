<?php

namespace App\Filament\Widgets;

use Filament\Widgets\Widget;
use Illuminate\Support\Facades\DB;

class AvailableCarsOverview extends Widget
{
    protected static string $view = 'filament.widgets.available-cars-overview';
    protected int | string | array $columnSpan = 'full';

    public function getViewData(): array
    {
        $cars = DB::table('cars')
            ->select('nama_mobil', DB::raw('count(*) as total'))
            ->where('status', 'ready')
            ->groupBy('nama_mobil')
            ->get();

        return [
            'cars' => $cars,
        ];
    }
}


