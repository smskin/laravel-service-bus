<?php

namespace SMSkin\ServiceBus\Packages;

use SMSkin\ServiceBus\Packages\Messages\TestMessage;
use SMSkin\ServiceBus\Packages\Processors\TestAsyncMessageProcessor;

class TestAsyncMessagePackage extends BasePackage
{
    protected function getMessageClass(): string
    {
        return TestMessage::class;
    }

    public function getProcessorClass(): string
    {
        return TestAsyncMessageProcessor::class;
    }
}
