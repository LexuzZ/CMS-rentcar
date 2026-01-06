<?php

namespace App\Filament\Resources\PenaltyResource\Pages;

use App\Filament\Resources\PenaltyResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditPenalty extends EditRecord
{
    protected static string $resource = PenaltyResource::class;
    protected static ?string $title = 'Edit Klaim Garasi';

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make()->label('Hapus Klaim Garasi'),
        ];
    }
    protected function afterSave(): void
    {
        $invoice = $this->record->booking?->invoice;

        if (!$invoice) {
            return;
        }

        // Paksa reload relasi penalty terbaru
        $invoice->load('booking.penalty');

        // Recalculate status payment
        $invoice->recalculatePaymentStatus();
    }


}
