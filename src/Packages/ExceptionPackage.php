<?php

namespace SMSkin\ServiceBus\Packages;

use SMSkin\ServiceBus\Enums\Packages;
use SMSkin\ServiceBus\Packages\Messages\ExceptionMessage;

class ExceptionPackage extends BasePackage
{
    protected function messageClass(): string
    {
        return ExceptionMessage::class;
    }

    public function package(): string
    {
        return Packages::EXCEPTION;
    }
}