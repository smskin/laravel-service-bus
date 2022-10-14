<?php

namespace SMSkin\ServiceBus\Packages\Processors;

use Illuminate\Support\Facades\Log;
use SMSkin\ServiceBus\Packages\BasePackage;
use SMSkin\ServiceBus\Packages\TestAsyncMessagePackage;

class TestAsyncMessageProcessor extends BaseProcessor
{
    public function __construct(protected TestAsyncMessagePackage|BasePackage $package)
    {
        parent::__construct($package);
    }

    public function execute(): BasePackage|null
    {
        Log::debug('Received async package', $this->package->toArray());
        return null;
    }
}
