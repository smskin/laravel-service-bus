<?php

namespace SMSkin\ServiceBus\Providers;

use SMSkin\ServiceBus\Support\ApiClient;

class ServiceProvider extends \Illuminate\Support\ServiceProvider
{
    public function boot()
    {
        $this->loadConfig();
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register(): void
    {
        $this->registerConfig();

        $this->app->register(RouteServiceProvider::class);
        $this->app->register(\SMSkin\ServiceBus\Support\NeedleProject\LaravelRabbitMq\Providers\ServiceProvider::class);
        $this->app->register(\SMSkin\ServiceBus\Support\Supervisor\Providers\ServiceProvider::class);

        $this->app->singleton(ApiClient::class, static function () {
            return new ApiClient();
        });
    }

    private function loadConfig()
    {
        $configPath = __DIR__ . '/../../config/service-bus.php';
        $this->publishes([
            $configPath => app()->configPath('service-bus.php'),
        ], 'service-bus');
    }

    private function registerConfig()
    {
        $configPath = __DIR__ . '/../../config/service-bus.php';
        $this->mergeConfigFrom($configPath, 'service-bus');
    }
}