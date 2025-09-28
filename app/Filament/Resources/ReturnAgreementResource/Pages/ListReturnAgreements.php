<?php

namespace App\Filament\Resources\ReturnAgreementResource\Pages;

use App\Filament\Resources\ReturnAgreementResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListReturnAgreements extends ListRecords
{
    protected static string $resource = ReturnAgreementResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
