<?php

namespace SMSkin\ServiceBus\Traits;

use SMSkin\ServiceBus\Enums\Connections;
use SMSkin\ServiceBus\Enums\Consumers;
use SMSkin\ServiceBus\Enums\Exchanges;
use SMSkin\ServiceBus\Enums\Hosts;
use SMSkin\ServiceBus\Enums\Packages;
use SMSkin\ServiceBus\Enums\Publishers;
use SMSkin\ServiceBus\Enums\Queues;

trait ClassFromConfig
{
    public static function getConnectionsEnum(): Connections
    {
        return app(config('service-bus.enums.connections'));
    }

    public static function getExchangesEnum(): Exchanges
    {
        return app(config('service-bus.enums.exchanges'));
    }

    public static function getQueuesEnum(): Queues
    {
        return app(config('service-bus.enums.queues'));
    }

    public static function getConsumersEnum(): Consumers
    {
        return app(config('service-bus.enums.consumers'));
    }

    public static function getPublishersEnum(): Publishers
    {
        return app(config('service-bus.enums.publishers'));
    }

    public static function getPackagesEnum(): Packages
    {
        return app(config('service-bus.enums.packages'));
    }

    public static function getHostsEnum(): Hosts
    {
        return app(config('service-bus.enums.hosts'));
    }
}
