<?php

namespace App\Filament\Resources\MonthlyResource\Pages;

use App\Filament\Resources\MonthlyResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListMonthlies extends ListRecords
{
    protected static string $resource = MonthlyResource::class;

    protected function getHeaderActions(): array
    {
        return [
            // Actions\CreateAction::make(),
        ];
    }
}
