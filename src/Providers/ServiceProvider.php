<?php

namespace SMSkin\ServiceBus\Providers;

use SMSkin\ServiceBus\Support\ApiClient;

class ServiceProvider extends \Illuminate\Support\ServiceProvider
{
    public function boot()
    {
        $this->loadConfig();

//        if (app()->runningInConsole()) {
//            $this->registerMigrations();
//        }
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->registerConfig();

        $this->app->register(RouteServiceProvider::class);
        $this->app->register(\SMSkin\ServiceBus\Support\NeedleProject\LaravelRabbitMq\Providers\ServiceProvider::class);

        $this->app->singleton(ApiClient::class, function () {
            return new ApiClient();
        });
    }

    private function loadConfig()
    {
        $configPath = __DIR__ . '/../../config/service-bus.php';
        $this->publishes([
            $configPath => app()->configPath('smskin/service-bus.php'),
        ], 'service-bus');
    }

    private function registerConfig()
    {
        $configPath = __DIR__ . '/../../config/service-bus.php';
        $this->mergeConfigFrom($configPath, 'smskin.service-bus');
    }

    private function registerMigrations()
    {
        $this->publishes([
            __DIR__ . '/../../migrations' => database_path('migrations'),
        ], 'service-bus');

        $this->loadMigrationsFrom(__DIR__ . '/../../migrations');
    }
}