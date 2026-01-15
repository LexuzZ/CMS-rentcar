<?php

// app/Observers/InvoiceObserver.php

namespace App\Observers;

use App\Models\Invoice;

class InvoiceObserver
{
    public function creating(Invoice $invoice): void
    {
        $invoice->total_tagihan ??= 0;
        $invoice->total_denda ??= 0;
        $invoice->total_paid ??= 0;
        $invoice->sisa_pembayaran ??= 0;
        $invoice->status ??= 'belum_lunas';
    }

    public function created(Invoice $invoice): void
    {
        $invoice->recalculate();
    }

    public function updated(Invoice $invoice): void
    {
        $invoice->recalculate();
    }
}
