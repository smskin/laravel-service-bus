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
    protected array $properties = [];

    public function setPackage(BasePackage $package): self
    {
        $this->package = $package;
        return $this;
    }

    public function setPublisher(string $publisher): self
    {
        $this->publisher = $publisher;
        return $this;
    }

    public function setRoutingKey(string $routingKey): self
    {
        $this->routingKey = $routingKey;
        return $this;
    }

    public function getPublisher(): string
    {
        return $this->publisher;
    }

    public function getPackage(): BasePackage
    {
        return $this->package;
    }

    public function getRoutingKey(): string
    {
        return $this->routingKey;
    }

    public function setProperties(array $properties): self
    {
        $this->properties = $properties;
        return $this;
    }

    public function getProperties(): array
    {
        return $this->properties;
    }
}
