<?php

namespace SMSkin\ServiceBus\Requests;

use SMSkin\ServiceBus\Packages\BasePackage;
use SMSkin\ServiceBus\Traits\ClassFromConfig;
use SMSkin\LaravelSupport\BaseRequest;

class AsyncPublishRequest extends BaseRequest
{
    use ClassFromConfig;

    protected string $publisher;
    protected BasePackage $package;
    protected string $routingKey;

    /**
     * @param BasePackage $package
     * @return AsyncPublishRequest
     */
    public function setPackage(BasePackage $package): AsyncPublishRequest
    {
        $this->package = $package;
        return $this;
    }

    /**
     * @param string $publisher
     * @return AsyncPublishRequest
     */
    public function setPublisher(string $publisher): AsyncPublishRequest
    {
        $this->publisher = $publisher;
        return $this;
    }

    /**
     * @param string $routingKey
     * @return AsyncPublishRequest
     */
    public function setRoutingKey(string $routingKey): AsyncPublishRequest
    {
        $this->routingKey = $routingKey;
        return $this;
    }

    /**
     * @return string
     */
    public function getPublisher(): string
    {
        return $this->publisher;
    }

    /**
     * @return BasePackage
     */
    public function getPackage(): BasePackage
    {
        return $this->package;
    }

    /**
     * @return string
     */
    public function getRoutingKey(): string
    {
        return $this->routingKey;
    }
}
