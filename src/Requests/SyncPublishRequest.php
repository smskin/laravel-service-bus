<?php

namespace SMSkin\ServiceBus\Requests;

use SMSkin\ServiceBus\Packages\BasePackage;
use SMSkin\ServiceBus\Traits\ClassFromConfig;
use SMSkin\LaravelSupport\BaseRequest;

class SyncPublishRequest extends BaseRequest
{
    use ClassFromConfig;

    protected string $host;
    protected BasePackage $package;

    public function setHost(string $host): self
    {
        $this->host = $host;
        return $this;
    }

    public function setPackage(BasePackage $package): self
    {
        $this->package = $package;
        return $this;
    }

    public function getHost(): string
    {
        return $this->host;
    }

    public function getPackage(): BasePackage
    {
        return $this->package;
    }
}
