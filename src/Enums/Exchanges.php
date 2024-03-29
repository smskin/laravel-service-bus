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

    private static Collection|null $items = null;

    /**
     * @return Collection<ExchangeItem>
     */
    public static function items(): Collection
    {
        if (static::$items !== null) {
            return static::$items;
        }

        return static::$items = static::getItems();
    }

    /**
     * @return Collection<ExchangeItem>
     */
    protected static function getItems(): Collection
    {
        return collect([
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

    public static function getByRabbitMqName(string $name): ExchangeItem
    {
        return static::items()->filter(static function (ExchangeItem $item) use ($name) {
            return $item->rabbitMqName === $name;
        })->firstOrFail();
    }
}
