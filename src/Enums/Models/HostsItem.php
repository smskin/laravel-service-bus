<?php

namespace SMSkin\ServiceBus\Enums\Models;

use SMSkin\LaravelSupport\Models\EnumItem;

class HostsItem extends EnumItem
{
    public string $host;

    public function setHost(string $host): self
    {
        $this->host = $host;
        return $this;
    }
}
