<?php

namespace SMSkin\ServiceBus\Enums\Models;

use SMSkin\LaravelSupport\Models\EnumItem;

class HostsItem extends EnumItem
{
    public string $host;

    /**
     * @param string $host
     * @return HostsItem
     */
    public function setHost(string $host): HostsItem
    {
        $this->host = $host;
        return $this;
    }
}
