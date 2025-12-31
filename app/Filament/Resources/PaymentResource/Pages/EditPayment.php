<?php

namespace App\Filament\Resources\PaymentResource\Pages;

use App\Filament\Resources\BookingResource;
use App\Filament\Resources\PaymentResource;
use App\Models\Payment;
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
    protected function afterSave(): void
    {
        $payment = $this->record;
        $invoice = $payment->invoice()->with('booking.penalty')->first();

        if (!$invoice) {
            return;
        }

        $biayaSewa = $invoice->booking?->estimasi_biaya ?? 0;
        $biayaAntar = $invoice->pickup_dropOff ?? 0;
        $totalDenda = $invoice->booking?->penalty->sum('amount') ?? 0;

        $totalTagihan = $biayaSewa + $biayaAntar + $totalDenda;

        // Hitung ulang DP dari SEMUA payment invoice ini
        $totalBayar = Payment::where('invoice_id', $invoice->id)->sum('pembayaran');

        $invoice->dp = $totalBayar;
        $invoice->sisa_pembayaran = max($totalTagihan - $totalBayar, 0);

        if ($invoice->sisa_pembayaran == 0) {
            $payment->updateQuietly(['status' => 'lunas']);
        }

        $invoice->save();
    }


}
