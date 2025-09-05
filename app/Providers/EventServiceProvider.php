<?php

namespace App\Providers;

use App\Models\Credit;
use App\Models\Payment;
use App\Observers\CreditObserver;
use App\Observers\PaymentObserver;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        Credit::observe(CreditObserver::class);
        Payment::observe(PaymentObserver::class);
    }
}
