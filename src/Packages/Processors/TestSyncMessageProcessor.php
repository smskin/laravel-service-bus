<?php

namespace SMSkin\ServiceBus\Packages\Processors;

use Illuminate\Support\Facades\Log;
use SMSkin\ServiceBus\Packages\BasePackage;
use SMSkin\ServiceBus\Packages\Messages\BaseMessage;
use SMSkin\ServiceBus\Packages\Messages\TestMessage;
use SMSkin\ServiceBus\Packages\TestSyncMessagePackage;
use SMSkin\ServiceBus\Traits\ClassFromConfig;

class TestSyncMessageProcessor extends BaseProcessor
{
    use ClassFromConfig;

    public function __construct(protected TestSyncMessagePackage|BasePackage $package)
    {
        parent::__construct($package);
    }

    public function execute(): ?BaseMessage
    {
        Log::debug('Received sync package', $this->package->toArray());

        return (new TestMessage)
            ->setString1('a1')
            ->setString2('b2');
    }
}
