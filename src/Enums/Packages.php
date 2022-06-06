<?php

namespace SMSkin\ServiceBus\Enums;

use Illuminate\Support\Collection;
use SMSkin\LaravelSupport\BaseEnum;
use SMSkin\ServiceBus\Enums\Models\PackageItem;
use SMSkin\ServiceBus\Packages\TestAsyncMessagePackage;
use SMSkin\ServiceBus\Packages\TestSyncMessageAnswerPackage;
use SMSkin\ServiceBus\Packages\TestSyncMessagePackage;

class Packages extends BaseEnum
{
    public const TEST_ASYNC = 'TEST_ASYNC';
    public const TEST_SYNC = 'TEST_SYNC';
    public const TEST_SYNC_ANSWER = 'TEST_SYNC_ANSWER';

    private static ?Collection $items = null;

    /**
     * @return Collection<PackageItem>
     */
    public static function items(): Collection
    {
        if (!is_null(static::$items)) {
            return static::$items;
        }

        return static::$items = static::getItems();
    }

    /**
     * @return Collection<PackageItem>
     */
    protected static function getItems(): Collection
    {
        return collect([
            (new PackageItem)
                ->setId(self::TEST_ASYNC)
                ->setClass(TestAsyncMessagePackage::class),
            (new PackageItem)
                ->setId(self::TEST_SYNC)
                ->setClass(TestSyncMessagePackage::class),
            (new PackageItem)
                ->setId(self::TEST_SYNC_ANSWER)
                ->setClass(TestSyncMessageAnswerPackage::class)
        ]);
    }
}
