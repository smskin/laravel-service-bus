<?php

namespace SMSkin\ServiceBus\Requests;

use SMSkin\ServiceBus\Packages\BasePackage;
use SMSkin\ServiceBus\Traits\ClassFromConfig;
use Illuminate\Validation\Rule;
use SMSkin\LaravelSupport\BaseRequest;
use SMSkin\LaravelSupport\Rules\InstanceOfRule;

class AsyncPublishRequest extends BaseRequest
{
    use ClassFromConfig;

    public string $publisher;
    public BasePackage $package;
    public string $routingKey;

    public function rules(): array
    {
        return [
            'publisher' => [
                'required',
                Rule::in(self::getPublishersEnum()::getKeys())
            ],
            'package' => [
                'required',
                new InstanceOfRule(BasePackage::class)
            ],
            'routingKey' => [
                'required',
                'string'
            ]
        ];
    }

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
}
