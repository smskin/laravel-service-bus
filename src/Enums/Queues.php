<?php

namespace SMSkin\ServiceBus\Enums;

use SMSkin\ServiceBus\Enums\Models\QueueItem;
use SMSkin\ServiceBus\Enums\Models\QueueItemAttributes;
use SMSkin\ServiceBus\Enums\Models\QueueItemBindAttribute;
use SMSkin\ServiceBus\Traits\ClassFromConfig;
use Illuminate\Support\Collection;
use SMSkin\LaravelSupport\BaseEnum;

class Queues extends BaseEnum
{
    use ClassFromConfig;

    public const DEFAULT = 'DEFAULT';

    private static Collection|null $items = null;

    /**
     * @return Collection<QueueItem>
     */
    public static function items(): Collection
    {
        if (static::$items !== null) {
            return static::$items;
        }

        return static::$items = static::getItems();
    }

    /**
     * @param string $exchangeId
     * @return Collection<QueueItem>
     */
    public static function getByExchangeId(string $exchangeId): Collection
    {
        return static::items()->filter(static function (QueueItem $item) use ($exchangeId) {
            $binds = $item->attributes->bind;
            return $binds->filter(static function (QueueItemBindAttribute $attribute) use ($exchangeId) {
                return $attribute->exchange === $exchangeId;
            })->first();
        });
    }

    /**
     * @return Collection<QueueItem>
     */
    protected static function getItems(): Collection
    {
        return collect([
            (new QueueItem())
                ->setId(self::DEFAULT)
                ->setConnection(self::getConnectionsEnum()::DEFAULT)
                ->setRabbitMqName('test-queue')
                ->setAttributes(
                    (new QueueItemAttributes)
                        ->setPassive(false)
                        ->setDurable(false)
                        ->setAutoDelete(false)
                        ->setInternal(false)
                        ->setNowait(false)
                        ->setExclusive(false)
                        ->setBind(collect([
                            (new QueueItemBindAttribute())
                                ->setExchange(self::getExchangesEnum()::DEFAULT)
                                ->setRoutingKey('*')
                        ]))
                        ->setArguments([
                            'x-max-priority' => 5
                        ])
                )
        ]);
    }
}
