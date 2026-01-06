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


        if ($invoice) {
            $invoice->payments()->update([
                'pembayaran' => $invoice->getTotalTagihan(),
            ]);
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
        // Ambil booking_id dari record
        $invoice = $this->record->invoice;

        if ($invoice) {
            // Redirect ke halaman View Invoice di Filament
            return \App\Filament\Resources\InvoiceResource::getUrl('view', [
                'record' => $invoice->id,
            ]);
        }

        // Kalau booking belum ada invoice, fallback ke index booking
        return BookingResource::getUrl('view', [
            'record' => $this->record->id,
        ]);
    }
}
