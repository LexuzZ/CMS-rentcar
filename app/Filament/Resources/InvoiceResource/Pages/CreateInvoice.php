<?php

namespace App\Filament\Resources\InvoiceResource\Pages;

use App\Filament\Resources\InvoiceResource;
use App\Models\Booking;
use Filament\Resources\Pages\CreateRecord;

class CreateInvoice extends CreateRecord
{
    protected static string $resource = InvoiceResource::class;
    protected static ?string $title = 'Tambah Faktur';

    public function mount(): void
    {
        parent::mount();

        if (!request()->filled('booking_id')) {
            return;
        }

        $booking = Booking::with('invoice')->find(request('booking_id'));

        // ❌ Cegah double invoice
        if ($booking?->invoice) {
            abort(403, 'Booking ini sudah memiliki faktur.');
        }

        // Prefill hanya field yang BENAR-BENAR ADA DI DB
        $this->form->fill([
            'booking_id' => $booking->id,
            'tanggal_invoice' => now(),
            'pickup_dropOff' => 0,
        ]);
    }
    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['total_tagihan'] = 0;
        $data['total_denda'] = 0;
        $data['total_paid'] = 0;
        $data['sisa_pembayaran'] = 0;
        $data['status'] = 'belum_lunas';

        return $data;
    }
    protected function afterCreate(): void
    {
        $this->record->refresh();
        $this->record->recalculate();
    }


}
