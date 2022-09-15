<?php

namespace SMSkin\ServiceBus\Requests;

use SMSkin\LaravelSupport\BaseRequest;

class ConsumeRequest extends BaseRequest
{
    protected string $json;

    /**
     * @param string $json
     * @return ConsumeRequest
     */
    public function setJson(string $json): ConsumeRequest
    {
        $this->json = $json;
        return $this;
    }

    /**
     * @return string
     */
    public function getJson(): string
    {
        return $this->json;
    }
}
