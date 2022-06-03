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

    public static function items(): Collection
    {
        if (!is_null(self::$items)) {
            return self::$items;
        }

        return self::$items = collect([
            (new PublisherItem())
                ->setId(self::TEST)
                ->setExchange(self::getExchangesEnum()::DEFAULT)
        ]);
    }
}
