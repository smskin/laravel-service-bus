<?php

namespace SMSkin\ServiceBus\Events;

use SMSkin\LaravelSupport\BaseEvent;
use SMSkin\ServiceBus\Packages\BasePackage;

class EPackageSubmitted extends BaseEvent
{
    public function __construct(public BasePackage $package, public string|null $publisher = null, public string|null $host = null)
    {
    }
}