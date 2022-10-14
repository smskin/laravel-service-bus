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

    private static Collection|null $items = null;

    /**
     * @return Collection<PublisherItem>
     */
    public static function items(): Collection
    {
        if (static::$items !== null) {
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

    public static function getByExchange(string $exchange): PublisherItem
    {
        return static::items()->filter(static function (PublisherItem $item) use ($exchange) {
            return $item->exchange === $exchange;
        })->firstOrFail();
    }
}
