<?php

namespace SMSkin\ServiceBus\Enums;

use SMSkin\ServiceBus\Enums\Models\ExchangeItem;
use SMSkin\ServiceBus\Enums\Models\ExchangeItemAttributes;
use SMSkin\ServiceBus\Traits\ClassFromConfig;
use Illuminate\Support\Collection;
use SMSkin\LaravelSupport\BaseEnum;

class Exchanges extends BaseEnum
{
    use ClassFromConfig;

    public const DEFAULT = 'DEFAULT';

    private static ?Collection $items = null;

    public static function items(): Collection
    {
        if (!is_null(self::$items)) {
            return self::$items;
        }

        return self::$items = collect([
            (new ExchangeItem())
                ->setId(self::DEFAULT)
                ->setConnection(self::getConnectionsEnum()::DEFAULT)
                ->setRabbitMqName('test-exchange')
                ->setAttributes(
                    (new ExchangeItemAttributes())
                        ->setExchangeType('topic')
                        ->setPassive(false)
                        ->setDurable(false)
                        ->setAutoDelete(false)
                        ->setInternal(false)
                        ->setNowait(false)
                        ->setThrowExceptionOnRedeclare(true)
                        ->setThrowExceptionOnBindFail(true)
                )
        ]);
    }
}
