<?php

namespace SMSkin\ServiceBus\Packages;

use SMSkin\ServiceBus\Enums\Packages;
use SMSkin\ServiceBus\Packages\Messages\UnknownMessage;

class IncomingPackage extends BasePackage
{
    protected function messageClass(): string
    {
        return UnknownMessage::class;
    }

    public function package(): string
    {
        return Packages::INCOMING;
    }
}
