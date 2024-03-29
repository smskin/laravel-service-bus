<?php

namespace SMSkin\ServiceBus\Enums;

use Illuminate\Support\Collection;
use SMSkin\LaravelSupport\BaseEnum;
use SMSkin\LaravelSupport\Models\EnumItem;

class Connections extends BaseEnum
{
    public const DEFAULT = 'DEFAULT';

    private static Collection|null $items = null;

    /**
     * @return Collection<EnumItem>
     */
    public static function items(): Collection
    {
        if (static::$items !== null) {
            return static::$items;
        }

        return static::$items = static::getItems();
    }

    /**
     * @return Collection<EnumItem>
     */
    protected static function getItems(): Collection
    {
        return collect([
            (new EnumItem())
                ->setId(self::DEFAULT)
        ]);
    }
}
