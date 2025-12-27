<?php

namespace App\Filament\Resources\InvoiceResource\Pages;

use App\Filament\Resources\BookingResource;
use App\Filament\Resources\InvoiceResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditInvoice extends EditRecord
{
    protected static string $resource = InvoiceResource::class;
    protected static ?string $title = 'Edit Faktur';

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make()->label('Hapus Faktur')->color('danger'),
        ];
    }
    protected function getRedirectUrl(): string
    {
        // Ambil data invoice yang baru saja dibuat
        $invoice = $this->getRecord();

        // Arahkan kembali ke halaman 'view' dari booking yang berelasi
        return BookingResource::getUrl('view', ['record' => $invoice->id]);
    }
}
