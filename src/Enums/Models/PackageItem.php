<?php

namespace SMSkin\ServiceBus\Enums\Models;

use SMSkin\LaravelSupport\Models\EnumItem;

class PackageItem extends EnumItem
{
    public string $class;
    public string $processor;

    /**
     * @param string $class
     * @return PackageItem
     */
    public function setClass(string $class): PackageItem
    {
        $this->class = $class;
        return $this;
    }

    /**
     * @param string $processor
     * @return PackageItem
     */
    public function setProcessor(string $processor): PackageItem
    {
        $this->processor = $processor;
        return $this;
    }
}
