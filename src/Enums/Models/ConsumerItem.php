<?php

namespace SMSkin\ServiceBus\Enums\Models;

use SMSkin\LaravelSupport\Models\EnumItem;

class ConsumerItem extends EnumItem
{
    public string $queue;
    public int $prefetchCount;

    public function setQueue(string $queue): self
    {
        $this->queue = $queue;
        return $this;
    }

    public function setPrefetchCount(int $prefetchCount): self
    {
        $this->prefetchCount = $prefetchCount;
        return $this;
    }
}
