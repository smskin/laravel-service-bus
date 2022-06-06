<?php

namespace SMSkin\ServiceBus\Support\Supervisor\Providers;

use Illuminate\Support\ServiceProvider as LaravelServiceProvider;
use SMSkin\ServiceBus\Support\Supervisor\Console\SupervisorCommand;

class ServiceProvider extends LaravelServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->commands([
            SupervisorCommand::class
        ]);
    }
}