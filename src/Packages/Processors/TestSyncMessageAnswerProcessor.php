<?php

namespace SMSkin\ServiceBus\Packages\Processors;

use Illuminate\Support\Facades\Log;
use SMSkin\ServiceBus\Packages\BasePackage;
use SMSkin\ServiceBus\Packages\TestSyncMessageAnswerPackage;

class TestSyncMessageAnswerProcessor extends BaseProcessor
{
    public function __construct(protected TestSyncMessageAnswerPackage|BasePackage $package)
    {
        parent::__construct($package);
    }

    public function execute(): ?BasePackage
    {
        Log::debug('Received sync package (answer)', $this->package->toArray());
        return null;
    }
}
