<?php

namespace SMSkin\ServiceBus\Packages\Processors;

use SMSkin\ServiceBus\Packages\BasePackage;
use SMSkin\ServiceBus\Packages\Messages\BaseMessage;

abstract class BaseProcessor
{
    public function __construct(protected BasePackage $package)
    {

    }

    abstract public function execute(): ?BaseMessage;
}
