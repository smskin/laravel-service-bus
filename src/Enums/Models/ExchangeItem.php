<?php

namespace SMSkin\ServiceBus\Enums\Models;

use SMSkin\LaravelSupport\Models\EnumItem;

class ExchangeItem extends EnumItem
{
    public string $connection;
    public string $rabbitMqName;
    public ExchangeItemAttributes $attributes;

    /**
     * @param string $rabbitMqName
     * @return ExchangeItem
     */
    public function setRabbitMqName(string $rabbitMqName): ExchangeItem
    {
        $this->rabbitMqName = $rabbitMqName;
        return $this;
    }

    /**
     * @param ExchangeItemAttributes $attributes
     * @return ExchangeItem
     */
    public function setAttributes(ExchangeItemAttributes $attributes): ExchangeItem
    {
        $this->attributes = $attributes;
        return $this;
    }

    /**
     * @param string $connection
     * @return ExchangeItem
     */
    public function setConnection(string $connection): ExchangeItem
    {
        $this->connection = $connection;
        return $this;
    }
}
