<?php

namespace App\Observers;

use App\Models\Penalty;

class PenaltyObserver
{
    public function created(Penalty $penalty): void
    {
        $this->recalculateInvoice($penalty);
    }

    public function updated(Penalty $penalty): void
    {
        $this->recalculateInvoice($penalty);
    }

    public function deleted(Penalty $penalty): void
    {
        $this->recalculateInvoice($penalty);
    }

    protected function recalculateInvoice(Penalty $penalty): void
    {
        $invoice = $penalty->booking?->invoice;

        if ($invoice) {
            $invoice->recalculate();
        }
    }
}
