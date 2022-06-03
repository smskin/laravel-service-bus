<?php

namespace SMSkin\ServiceBus\Requests;

use SMSkin\LaravelSupport\BaseRequest;

class ConsumeRequest extends BaseRequest
{
    public string $json;

    public function rules(): array
    {
        return [
            'json' => [
                'required'
            ]
        ];
    }

    /**
     * @param string $json
     * @return ConsumeRequest
     */
    public function setJson(string $json): ConsumeRequest
    {
        $this->json = $json;
        return $this;
    }
}
