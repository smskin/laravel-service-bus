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

    /**
     * @param string $host
     * @return SyncPublishRequest
     */
    public function setHost(string $host): SyncPublishRequest
    {
        $this->host = $host;
        return $this;
    }

    /**
     * @param BasePackage $package
     * @return SyncPublishRequest
     */
    public function setPackage(BasePackage $package): SyncPublishRequest
    {
        $this->package = $package;
        return $this;
    }

    /**
     * @return string
     */
    public function getHost(): string
    {
        return $this->host;
    }

    /**
     * @return BasePackage
     */
    public function getPackage(): BasePackage
    {
        return $this->package;
    }
}
