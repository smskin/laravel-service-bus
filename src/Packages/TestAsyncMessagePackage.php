<?php

namespace SMSkin\ServiceBus\Packages;

use SMSkin\ServiceBus\Packages\Messages\TestMessage;

class TestAsyncMessagePackage extends BasePackage
{
    protected function getMessageClass(): string
    {
        return TestMessage::class;
    }
}
