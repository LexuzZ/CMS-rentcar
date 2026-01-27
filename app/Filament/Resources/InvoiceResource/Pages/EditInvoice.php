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
            Actions\Action::make('view-booking')
                ->label('Detail Invoice')
                ->icon('heroicon-o-arrow-left')
                ->url(function () {
                    $invoice = $this->record;
                    if ($invoice) {
                        // redirect ke halaman view booking di Filament
                        return \App\Filament\Resources\InvoiceResource::getUrl('view', [
                            'record' => $invoice->id,
                        ]);
                    }

                    // fallback kalau tidak ada booking
                    return \App\Filament\Resources\InvoiceResource::getUrl();
                })
                ->color('gray'),
        ];
    }
    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

}
