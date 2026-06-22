<?php

namespace App\Providers;

use App\Models\WeddingCard;
use App\Policies\WeddingCardPolicy;
use App\Services\Contracts\PaymentGatewayInterface;
use App\Services\ToyyibPayService;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(PaymentGatewayInterface::class, ToyyibPayService::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Gate::policy(WeddingCard::class, WeddingCardPolicy::class);
    }
}
