<?php

namespace App\Providers;

use App\Models\Credit;
use Illuminate\Support\ServiceProvider;
use App\Contracts\WhatsAppGateway;
use App\Services\NestWhatsAppGateway;
use App\Observers\CreditObserver;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->singleton(WhatsAppGateway::class, function () {
            return new NestWhatsAppGateway(
                config('services.nest.base_url'),
                config('services.nest.api_key'),
            );
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Credit::observe(CreditObserver::class);
    }
}
