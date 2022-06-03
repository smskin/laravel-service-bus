<?php

namespace SMSkin\ServiceBus\Enums\Models;

use SMSkin\LaravelSupport\Models\EnumItem;

class PublisherItem extends EnumItem
{
    public string $exchange;

    /**
     * @param string $exchange
     * @return PublisherItem
     */
    public function setExchange(string $exchange): PublisherItem
    {
        $this->exchange = $exchange;
        return $this;
    }
}
