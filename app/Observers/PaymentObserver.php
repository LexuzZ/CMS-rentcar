<?php

// app/Observers/PaymentObserver.php

namespace App\Observers;

use App\Models\Payment;

class PaymentObserver
{
    public function created(Payment $payment): void
    {
        $payment->invoice?->recalculate();
    }

    public function deleted(Payment $payment): void
    {
        $payment->invoice?->recalculate();
    }

    public function updated(Payment $payment): void
    {
        $payment->invoice?->recalculate();
    }
}
