<?php

namespace SMSkin\ServiceBus\Requests;

use SMSkin\LaravelSupport\BaseRequest;

class ConsumeRequest extends BaseRequest
{
    protected string $json;

    public function setJson(string $json): self
    {
        $this->json = $json;
        return $this;
    }

    public function getJson(): string
    {
        return $this->json;
    }
}
