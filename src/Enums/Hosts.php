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
        if (!is_null(self::$items)) {
            return self::$items;
        }

        return self::$items = collect([
            (new HostsItem)
                ->setId(self::LOCALHOST)
                ->setHost('http://nginx/' . config('service-bus.host.route_prefix') . '/consumer')
        ]);
    }
}
