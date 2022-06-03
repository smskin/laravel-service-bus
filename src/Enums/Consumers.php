<?php

namespace SMSkin\ServiceBus\Enums;

use SMSkin\ServiceBus\Enums\Models\ConsumerItem;
use SMSkin\ServiceBus\Traits\ClassFromConfig;
use Illuminate\Support\Collection;
use SMSkin\LaravelSupport\BaseEnum;

class Consumers extends BaseEnum
{
    use ClassFromConfig;

    public const TEST = 'TEST_ASYNC';

    private static ?Collection $items = null;

    public static function items(): Collection
    {
        if (!is_null(self::$items)) {
            return self::$items;
        }

        return self::$items = collect([
            (new ConsumerItem)
                ->setId(self::TEST)
                ->setQueue(self::getQueuesEnum()::DEFAULT)
                ->setPrefetchCount(10)
        ]);
    }
}
