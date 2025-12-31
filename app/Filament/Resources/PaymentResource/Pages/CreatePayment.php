<?php

namespace App\Filament\Resources\PaymentResource\Pages;

use App\Filament\Resources\BookingResource;
use App\Filament\Resources\PaymentResource;
use App\Models\Invoice;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreatePayment extends CreateRecord
{
    protected static string $resource = PaymentResource::class;
    protected static ?string $title = 'Tambah Pembayaran';
    public function mount(): void
    {
        // Cek apakah ada 'booking_id' di URL
        if (request()->has('invoice_id')) {
            $invoiceId = request('invoice_id');
            $invoice = Invoice::with('booking.penalty')->find($invoiceId);

            if ($invoice) {
                $totalInvoice = $invoice->total;
                $totalDenda = $invoice->booking?->penalty->sum('amount') ?? 0;

                $this->form->fill([
                    'invoice_id' => $invoiceId,
                    'pembayaran' => $totalInvoice + $totalDenda,
                ]);
            }
        }
    }
    protected function getRedirectUrl(): string
    {
        // Ambil data pembayaran yang baru saja dibuat
        $payment = $this->getRecord();

        // Arahkan kembali ke halaman 'view' dari booking yang berelasi
        // melalui relasi invoice
        return BookingResource::getUrl('view', ['record' => $payment->invoice->booking_id]);
    }
    protected function afterCreate(): void
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

        // Simpan DP & sisa
        $invoice->dp = ($invoice->dp ?? 0) + $payment->pembayaran;
        $invoice->sisa_pembayaran = max($totalTagihan - $invoice->dp, 0);

        // Auto status
        if ($invoice->sisa_pembayaran == 0) {
            $payment->updateQuietly(['status' => 'lunas']);
        }

        $invoice->save();
    }

}
