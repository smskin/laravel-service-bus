<?php

namespace SMSkin\ServiceBus\Packages;

use SMSkin\ServiceBus\Enums\Packages;
use SMSkin\ServiceBus\Packages\Messages\TestMessage;
use SMSkin\ServiceBus\Packages\Processors\TestSyncMessageProcessor;

class TestSyncMessagePackage extends BasePackage
{
    public function package(): string
    {
        return Packages::TEST_SYNC;
    }

    protected function messageClass(): string
    {
        return TestMessage::class;
    }

    public function getProcessorClass(): string
    {
        return TestSyncMessageProcessor::class;
    }
}
