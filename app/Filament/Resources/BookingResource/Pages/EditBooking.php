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
        $status = $this->record->status;
        $car = $this->record->car;
        $invoice = $this->record->invoice;

        if (!$car) {
            return;
        }

        if (in_array($status, ['selesai', 'batal']) && $car->status !== 'ready') {
            $car->update(['status' => 'ready']);
            return;
        }

        if ($status === 'disewa' && $car->status !== 'disewa') {
            $car->update(['status' => 'disewa']);
        }
    }



    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make()->label('Hapus Pesanan'),
        ];
    }
    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
