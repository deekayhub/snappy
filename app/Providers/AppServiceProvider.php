<?php

namespace App\Providers;

use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;
use Illuminate\Pagination\Paginator;
use Laravel\Cashier\Cashier;


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
        Paginator::useBootstrap();

        Cashier::$registersRoutes = false;

        Route::post(
            '/' . config('cashier.path') . '/webhook',
            'App\Http\Controllers\WebhookController@handleWebhook'
        )->name('cashier.webhook');
    }
}
