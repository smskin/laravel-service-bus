<?php

namespace SMSkin\ServiceBus\Events;

use SMSkin\LaravelSupport\BaseEvent;
use SMSkin\ServiceBus\Packages\BasePackage;

class EPackageConsumed extends BaseEvent
{
    public function __construct(public BasePackage $package)
    {
    }
}