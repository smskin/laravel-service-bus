<?php

namespace SMSkin\ServiceBus\Packages\Processors;

use SMSkin\ServiceBus\Packages\BasePackage;

abstract class BaseProcessor
{
    public function __construct(protected BasePackage $package)
    {

    }

    abstract public function execute(): ?BasePackage;
}
