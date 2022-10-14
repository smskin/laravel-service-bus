<?php

namespace SMSkin\ServiceBus\Enums\Models;

use SMSkin\LaravelSupport\Models\EnumItem;

class ExchangeItem extends EnumItem
{
    public string $connection;
    public string $rabbitMqName;
    public ExchangeItemAttributes $attributes;

    public function setRabbitMqName(string $rabbitMqName): self
    {
        $this->rabbitMqName = $rabbitMqName;
        return $this;
    }

    public function setAttributes(ExchangeItemAttributes $attributes): self
    {
        $this->attributes = $attributes;
        return $this;
    }

    public function setConnection(string $connection): self
    {
        $this->connection = $connection;
        return $this;
    }
}
