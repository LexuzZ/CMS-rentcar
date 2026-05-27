<?php

namespace App\Filament\Resources\CarInstallmentResource\Pages;

use App\Filament\Resources\CarInstallmentResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListCarInstallments extends ListRecords
{
    protected static string $resource = CarInstallmentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
