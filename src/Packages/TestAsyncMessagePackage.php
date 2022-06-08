<?php

namespace SMSkin\ServiceBus\Packages;

use SMSkin\ServiceBus\Enums\Packages;
use SMSkin\ServiceBus\Packages\Messages\TestMessage;
use SMSkin\ServiceBus\Packages\Processors\TestAsyncMessageProcessor;

class TestAsyncMessagePackage extends BasePackage
{
    public function package(): string
    {
        return Packages::TEST_ASYNC;
    }

    protected function messageClass(): string
    {
        return TestMessage::class;
    }

    public function getProcessorClass(): string
    {
        return TestAsyncMessageProcessor::class;
    }
}
