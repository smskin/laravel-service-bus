<?php

namespace SMSkin\ServiceBus\Packages;

use SMSkin\ServiceBus\Packages\Messages\TestMessage;
use SMSkin\ServiceBus\Packages\Processors\TestSyncMessageProcessor;

class TestSyncMessagePackage extends BasePackage
{
    protected function getMessageClass(): string
    {
        return TestMessage::class;
    }

    public function getProcessorClass(): string
    {
        return TestSyncMessageProcessor::class;
    }
}
