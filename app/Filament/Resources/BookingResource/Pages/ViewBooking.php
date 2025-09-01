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
            Actions\Action::make('edit_invoice')
                ->label('Edit Faktur')
                ->icon('heroicon-o-pencil-square')
                ->url(function () {
                    $invoice = $this->record->invoice;
                    if ($invoice) {
                        return \App\Filament\Resources\InvoiceResource::getUrl('view', [
                            'record' => $invoice->id,
                        ]);
                    }

                    // fallback kalau belum ada invoice
                    return \App\Filament\Resources\InvoiceResource::getUrl();
                })
                ->color('warning')
                ->visible(fn () => $this->record->invoice !== null),

        ];
    }
}
