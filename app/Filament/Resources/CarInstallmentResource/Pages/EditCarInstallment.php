<?php

namespace App\Filament\Resources\CarInstallmentResource\Pages;

use App\Filament\Resources\CarInstallmentResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditCarInstallment extends EditRecord
{
    protected static string $resource = CarInstallmentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
