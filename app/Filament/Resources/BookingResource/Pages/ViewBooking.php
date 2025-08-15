<?php

namespace App\Filament\Resources\BookingResource\Pages;

use App\Filament\Resources\BookingResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewBooking extends ViewRecord
{
    protected static string $resource = BookingResource::class;

    protected function getHeaderActions(): array
    {
        return [
            // Tombol Edit akan otomatis muncul di sini jika pengguna memiliki izin
            Actions\EditAction::make(),
            Actions\DeleteAction::make(),

        ];
    }
}
