<?php

namespace SMSkin\ServiceBus\Enums;

use SMSkin\ServiceBus\Enums\Models\HostsItem;
use Illuminate\Support\Collection;
use SMSkin\LaravelSupport\BaseEnum;

class Hosts extends BaseEnum
{
    public const LOCALHOST = 'LOCALHOST';

    private static ?Collection $items = null;

    /**
     * @return Collection<HostsItem>
     */
    public static function items(): Collection
    {
        if (!is_null(static::$items)) {
            return static::$items;
        }

        return static::$items = static::getItems();
    }

    /**
     * @return Collection<HostsItem>
     */
    protected static function getItems(): Collection
    {
        return collect([
            (new HostsItem)
                ->setId(self::LOCALHOST)
                ->setHost('http://nginx/' . config('service-bus.host.route_prefix') . '/consumer')
        ]);
    }
}
