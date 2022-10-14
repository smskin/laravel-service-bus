<?php

namespace SMSkin\ServiceBus\Enums\Models;

use SMSkin\LaravelSupport\Models\EnumItem;

class QueueItem extends EnumItem
{
    public string $connection;
    public string $rabbitMqName;
    public QueueItemAttributes $attributes;

    public function setRabbitMqName(string $rabbitMqName): self
    {
        $this->rabbitMqName = $rabbitMqName;
        return $this;
    }

    public function setConnection(string $connection): self
    {
        $this->connection = $connection;
        return $this;
    }

    public function setAttributes(QueueItemAttributes $attributes): self
    {
        $this->attributes = $attributes;
        return $this;
    }
}
