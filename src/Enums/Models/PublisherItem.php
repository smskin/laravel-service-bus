<?php

namespace SMSkin\ServiceBus\Enums\Models;

use SMSkin\LaravelSupport\Models\EnumItem;

class PublisherItem extends EnumItem
{
    public string $exchange;

    public function setExchange(string $exchange): self
    {
        $this->exchange = $exchange;
        return $this;
    }
}
