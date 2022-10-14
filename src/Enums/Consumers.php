<?php

namespace SMSkin\ServiceBus\Enums;

use SMSkin\ServiceBus\Enums\Models\ConsumerItem;
use SMSkin\ServiceBus\Traits\ClassFromConfig;
use Illuminate\Support\Collection;
use SMSkin\LaravelSupport\BaseEnum;

class Consumers extends BaseEnum
{
    use ClassFromConfig;

    public const TEST = 'TEST';

    private static Collection|null $items = null;

    /**
     * @return Collection<ConsumerItem>
     */
    public static function items(): Collection
    {
        if (static::$items !== null) {
            return static::$items;
        }

        return static::$items = static::getItems();
    }

    /**
     * @return Collection<ConsumerItem>
     */
    protected static function getItems(): Collection
    {
        return collect([
            (new ConsumerItem)
                ->setId(self::TEST)
                ->setQueue(self::getQueuesEnum()::DEFAULT)
                ->setPrefetchCount(10)
        ]);
    }
}
