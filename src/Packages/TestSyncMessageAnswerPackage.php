<?php

namespace SMSkin\ServiceBus\Packages;

use SMSkin\ServiceBus\Packages\Messages\TestMessage;

class TestSyncMessageAnswerPackage extends BasePackage
{
    protected function getMessageClass(): string
    {
        return TestMessage::class;
    }
}
