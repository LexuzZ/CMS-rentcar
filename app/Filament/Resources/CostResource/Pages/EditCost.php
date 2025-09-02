<?php

namespace App\Filament\Resources\CostResource\Pages;

use App\Filament\Resources\CostResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditCost extends EditRecord
{
    protected static string $resource = CostResource::class;
    protected static ?string $title = 'Edit Kas Keluar';

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make()->label('Hapus Kas Keluar'),
        ];
    }
}
