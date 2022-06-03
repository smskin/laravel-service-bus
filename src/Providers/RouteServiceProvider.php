<?php

namespace SMSkin\ServiceBus\Providers;

use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Route;
use SMSkin\ServiceBus\Http\Middleware\ApiToken;

class RouteServiceProvider extends ServiceProvider
{
    /**
     * Define your route model bindings, pattern filters, and other route configuration.
     *
     * @return void
     */
    public function boot()
    {
        $this->routes(function () {
            $this->mapRoutes();
        });
    }

    private function mapRoutes()
    {
        if (!config('smskin.service-bus.host.active')) {
            return;
        }

        Route::middleware([
            ApiToken::class
        ])
            ->name('vendors.smskin.service-bus.')
            ->namespace('SMSkin\ServiceBus\Http\Controllers')
            ->prefix(config('smskin.service-bus.host.route_prefix'))
            ->group(__DIR__ . '/../../routes/routes.php');
    }
}