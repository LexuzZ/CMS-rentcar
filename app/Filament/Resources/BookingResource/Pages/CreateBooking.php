<?php

namespace App\Filament\Resources\BookingResource\Pages;

use App\Filament\Resources\BookingResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateBooking extends CreateRecord
{
    protected static string $resource = BookingResource::class;
    protected static ?string $title = 'Tambah Pesanan';
    protected function afterCreate(): void
    {
        if (
            $this->record->status === 'disewa'
            && $this->record->car?->status !== 'disewa'
        ) {
            $this->record->car()->update([
                'status' => 'disewa',
            ]);
        }
    }

    protected function getRedirectUrl(): string
    {
        return BookingResource::getUrl('view', [
            'record' => $this->record,
        ]);
    }


}
