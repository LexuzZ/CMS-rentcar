<?php

namespace App\Filament\Resources\ReturnAgreementResource\Pages;

use App\Filament\Resources\ReturnAgreementResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditReturnAgreement extends EditRecord
{
    protected static string $resource = ReturnAgreementResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
