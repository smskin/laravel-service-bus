<?php

namespace SMSkin\ServiceBus\Enums\Models;

use SMSkin\LaravelSupport\Models\EnumItem;

class PackageItem extends EnumItem
{
    public string $class;

    /**
     * @param string $class
     * @return PackageItem
     */
    public function setClass(string $class): PackageItem
    {
        $this->class = $class;
        return $this;
    }
}
