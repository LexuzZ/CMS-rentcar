<?php

namespace App\Providers;

use App\Models\Booking;
use App\Models\Invoice;
use App\Models\Payment;
use App\Models\Penalty;
use App\Notifications\MobilKembaliNotification;
use App\Observers\InvoiceObserver;
use App\Observers\PaymentObserver;
use App\Observers\PenaltyObserver;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Invoice::observe(InvoiceObserver::class);
        Payment::observe(PaymentObserver::class);
        Penalty::observe(PenaltyObserver::class);
    }
}
