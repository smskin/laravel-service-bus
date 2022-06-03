<?php

namespace SMSkin\ServiceBus\Requests;

use SMSkin\ServiceBus\Packages\BasePackage;
use SMSkin\ServiceBus\Traits\ClassFromConfig;
use Illuminate\Validation\Rule;
use SMSkin\LaravelSupport\BaseRequest;
use SMSkin\LaravelSupport\Rules\InstanceOfRule;

class SyncPublishRequest extends BaseRequest
{
    use ClassFromConfig;

    public string $host;
    public BasePackage $package;

    public function rules(): array
    {
        return [
            'host' => [
                'required',
                Rule::in(self::getHostsEnum()::getKeys())
            ],
            'package' => [
                'required',
                new InstanceOfRule(BasePackage::class)
            ],
        ];
    }

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
}
