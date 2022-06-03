<?php

namespace SMSkin\ServiceBus\Enums\Models;

use SMSkin\ServiceBus\Traits\ClassFromConfig;
use Illuminate\Contracts\Support\Arrayable;
use SMSkin\LaravelSupport\Models\EnumItem;

class QueueItemBindAttribute implements Arrayable
{
    use ClassFromConfig;

    public string $exchange;
    public string $routingKey;

    public function toArray(): array
    {
        return [
            'exchange' => $this->getExchange()->rabbitMqName,
            'routing_key' => $this->routingKey
        ];
    }

    /**
     * @param string $exchange
     * @return QueueItemBindAttribute
     */
    public function setExchange(string $exchange): QueueItemBindAttribute
    {
        $this->exchange = $exchange;
        return $this;
    }

    /**
     * @param string $routingKey
     * @return QueueItemBindAttribute
     */
    public function setRoutingKey(string $routingKey): QueueItemBindAttribute
    {
        $this->routingKey = $routingKey;
        return $this;
    }

    private function getExchange(): ExchangeItem|EnumItem
    {
        return $this->getExchangesEnum()::getById($this->exchange);
    }
}
