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
        return $this->getResource()::getUrl('index');
    }




}
