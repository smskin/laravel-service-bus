<?php

namespace SMSkin\ServiceBus\Packages;

use SMSkin\ServiceBus\Packages\Messages\UnknownMessage;

class IncomingPackage extends BasePackage
{
    protected function getMessageClass(): string
    {
        return UnknownMessage::class;
    }
}
