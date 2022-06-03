<?php

namespace SMSkin\ServiceBus\Enums\Models;

use SMSkin\LaravelSupport\Models\EnumItem;

class ConsumerItem extends EnumItem
{
    public string $queue;
    public int $prefetchCount;

    /**
     * @param string $queue
     * @return ConsumerItem
     */
    public function setQueue(string $queue): ConsumerItem
    {
        $this->queue = $queue;
        return $this;
    }

    /**
     * @param int $prefetchCount
     * @return ConsumerItem
     */
    public function setPrefetchCount(int $prefetchCount): ConsumerItem
    {
        $this->prefetchCount = $prefetchCount;
        return $this;
    }
}
