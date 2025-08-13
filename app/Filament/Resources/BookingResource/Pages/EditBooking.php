<?php

namespace App\Filament\Resources\BookingResource\Pages;

use App\Filament\Resources\BookingResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditBooking extends EditRecord
{
    protected static string $resource = BookingResource::class;
    protected function afterSave(): void
    {
        if (in_array($this->record->status, ['selesai', 'batal'])) {
            $this->record->car->update([
                'status' => 'ready',
            ]);
        }

        if ($this->record->status === 'disewa' || $this->record->status === 'booking') {
            $this->record->car->update([
                'status' => 'disewa',
            ]);
        }
    }


    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
