<?php

namespace App\Filament\Pages\Worksheet\Widgets;

use App\Models\Car;
use Carbon\Carbon;
use Filament\Widgets\Widget;
use Illuminate\Support\Facades\DB;

class AvailableCarsOverview extends Widget
{
    protected static string $view = 'filament.widgets.available-cars-overview';
    protected int|string|array $columnSpan = 'full';

    public function getViewData(): array
{
    $today = Carbon::today();
    $tomorrow = Carbon::tomorrow();

    $availableCars = Car::with(['carModel', 'bookings'])
        ->where('garasi', 'SPT')
        ->where(function ($query) use ($today) {
            $query
                // Mobil tanpa booking aktif
                ->whereDoesntHave('bookings', function ($q) use ($today) {
                    $q->whereDate('tanggal_keluar', '<=', $today);
                })
                // ATAU booking tapi keluarnya BESOK ke atas
                ->orWhereHas('bookings', function ($q) use ($today) {
                    $q->whereDate('tanggal_keluar', '>', $today);
                });
        })
        ->get();

    $groupedCars = $availableCars->groupBy('carModel.name');

    return [
        'cars' => $groupedCars,
    ];
}

}


