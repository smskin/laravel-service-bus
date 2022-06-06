<?php

namespace SMSkin\ServiceBus\Enums;

use SMSkin\ServiceBus\Enums\Models\PublisherItem;
use SMSkin\ServiceBus\Traits\ClassFromConfig;
use Illuminate\Support\Collection;
use SMSkin\LaravelSupport\BaseEnum;

class Publishers extends BaseEnum
{
    use ClassFromConfig;

    public const TEST = 'TEST';

    private static ?Collection $items = null;

    /**
     * @return Collection<PublisherItem>
     */
    public static function items(): Collection
    {
        if (!is_null(static::$items)) {
            return static::$items;
        }

        return static::$items = static::getItems();
    }

    /**
     * @return Collection<PublisherItem>
     */
    protected static function getItems(): Collection
    {
        return collect([
            (new PublisherItem())
                ->setId(self::TEST)
                ->setExchange(self::getExchangesEnum()::DEFAULT)
        ]);
    }
}
