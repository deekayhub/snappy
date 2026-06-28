<?php

namespace App\Providers;

use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;
use Illuminate\Pagination\Paginator;
use Laravel\Cashier\Cashier;
use Laravel\Cashier\Http\Controllers\PaymentController;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        Paginator::useBootstrap();

        Cashier::ignoreRoutes();

        Route::prefix(config('cashier.path'))
            ->name('cashier.')
            ->group(function () {
                Route::get('payment/{id}', [PaymentController::class, 'show'])->name('payment');
                Route::post('webhook', [\App\Http\Controllers\WebhookController::class, 'handleWebhook'])->name('webhook');
            });

        Blade::if('hasFeature', function (string $feature) {
            return auth()->check() && auth()->user()->hasFeature($feature);
        });
    }
}
