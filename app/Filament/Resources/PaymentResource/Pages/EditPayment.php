<?php

namespace App\Filament\Resources\PaymentResource\Pages;

use App\Filament\Resources\BookingResource;
use App\Filament\Resources\PaymentResource;
use App\Models\Invoice;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditPayment extends EditRecord
{
    protected static string $resource = PaymentResource::class;
    protected static ?string $title = 'Edit Pembayaran';

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make()->label('Hapus Pembayaran'),
        ];
    }
    protected function getRedirectUrl(): string
    {
        // Ambil data pembayaran yang baru saja dibuat
        $payment = $this->getRecord();

        // Arahkan kembali ke halaman 'view' dari booking yang berelasi
        // melalui relasi invoice
        return BookingResource::getUrl('view', ['record' => $payment->invoice->booking_id]);
    }
    protected function mutateFormDataBeforeSave(array $data): array
    {
        if (!empty($data['invoice_id'])) {
            $invoice = Invoice::with('booking.penalty')->find($data['invoice_id']);

            if ($invoice) {
                $data['pembayaran'] = $invoice->getTotalTagihan();
            }
        }

        return $data;
    }



}
