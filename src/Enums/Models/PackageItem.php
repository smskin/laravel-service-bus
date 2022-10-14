<?php

namespace SMSkin\ServiceBus\Enums\Models;

use SMSkin\LaravelSupport\Models\EnumItem;

class PackageItem extends EnumItem
{
    public string $class;

    public function setClass(string $class): self
    {
        $this->class = $class;
        return $this;
    }
}
