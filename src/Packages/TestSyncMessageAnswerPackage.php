<?php

namespace SMSkin\ServiceBus\Packages;

use SMSkin\ServiceBus\Enums\Packages;
use SMSkin\ServiceBus\Packages\Messages\TestMessage;

class TestSyncMessageAnswerPackage extends BasePackage
{
    public function package(): string
    {
        return Packages::TEST_SYNC_ANSWER;
    }

    protected function messageClass(): string
    {
        return TestMessage::class;
    }
}
