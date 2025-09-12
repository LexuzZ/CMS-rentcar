<?php

namespace App\Filament\Resources\ServiceHistoryResource\Pages;

use App\Filament\Resources\ServiceHistoryResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListServiceHistories extends ListRecords
{
    protected static string $resource = ServiceHistoryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()->label('Riwayat Service') // ubah teks tombol
                ->icon('heroicon-o-plus')
                ->color('success'),
        ];
    }
}
