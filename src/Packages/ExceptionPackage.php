<?php

namespace SMSkin\ServiceBus\Packages;

use SMSkin\ServiceBus\Packages\Messages\ExceptionMessage;

class ExceptionPackage extends BasePackage
{
    protected function getMessageClass(): string
    {
        return ExceptionMessage::class;
    }
}