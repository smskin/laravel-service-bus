<?php

namespace SMSkin\ServiceBus\Enums;

use Illuminate\Support\Collection;
use SMSkin\LaravelSupport\BaseEnum;
use SMSkin\LaravelSupport\Models\EnumItem;

class Connections extends BaseEnum
{
    public const DEFAULT = 'DEFAULT';

    private static ?Collection $items = null;

    /**
     * @return Collection<EnumItem>
     */
    public static function items(): Collection
    {
        if (!is_null(self::$items)) {
            return self::$items;
        }

        return self::$items = collect([
            (new EnumItem())
                ->setId(self::DEFAULT)
        ]);
    }
}
