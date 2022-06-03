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
        return app(config('esb.enums.connections'));
    }

    public static function getExchangesEnum(): Exchanges
    {
        return app(config('esb.enums.exchanges'));
    }

    public static function getQueuesEnum(): Queues
    {
        return app(config('esb.enums.queues'));
    }

    public static function getConsumersEnum(): Consumers
    {
        return app(config('esb.enums.consumers'));
    }

    public static function getPublishersEnum(): Publishers
    {
        return app(config('esb.enums.publishers'));
    }

    public static function getPackagesEnum(): Packages
    {
        return app(config('esb.enums.packages'));
    }

    public static function getHostsEnum(): Hosts
    {
        return app(config('esb.enums.hosts'));
    }
}
