<?php

namespace SMSkin\ServiceBus\Packages\Processors;

use Illuminate\Support\Facades\Log;
use SMSkin\ServiceBus\Enums\Packages;
use SMSkin\ServiceBus\Packages\BasePackage;
use SMSkin\ServiceBus\Packages\Messages\TestMessage;
use SMSkin\ServiceBus\Packages\TestSyncMessageAnswerPackage;
use SMSkin\ServiceBus\Packages\TestSyncMessagePackage;
use SMSkin\ServiceBus\Traits\ClassFromConfig;
use function now;

class TestSyncMessageProcessor extends BaseProcessor
{
    use ClassFromConfig;

    public function __construct(protected TestSyncMessagePackage|BasePackage $package)
    {
        parent::__construct($package);
    }

    public function execute(): ?BasePackage
    {
        Log::debug('Received sync package', $this->package->toArray());

        return (new TestSyncMessageAnswerPackage)
            ->setPackage(Packages::TEST_SYNC_ANSWER)
            ->setCorrelationId($this->package->getCorrelationId())
            ->setSentTime(now())
            ->setMessage(
                (new TestMessage)
                    ->setString1('a1')
                    ->setString2('b2')
            );
    }
}
