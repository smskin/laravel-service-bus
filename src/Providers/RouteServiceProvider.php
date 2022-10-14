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
    public function boot(): void
    {
        $this->routes(function () {
            $this->mapRoutes();
        });
    }

    private function mapRoutes()
    {
        if (!config('service-bus.host.active')) {
            return;
        }

        Route::middleware([
            ApiToken::class
        ])
            ->name('service-bus.')
            ->namespace('SMSkin\ServiceBus\Http\Controllers')
            ->prefix(config('service-bus.host.route_prefix'))
            ->group(__DIR__ . '/../../routes/routes.php');
    }
}