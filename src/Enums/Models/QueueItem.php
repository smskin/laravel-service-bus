<?php

namespace SMSkin\ServiceBus\Enums\Models;

use SMSkin\LaravelSupport\Models\EnumItem;

class QueueItem extends EnumItem
{
    public string $connection;
    public string $rabbitMqName;
    public QueueItemAttributes $attributes;

    /**
     * @param string $rabbitMqName
     * @return QueueItem
     */
    public function setRabbitMqName(string $rabbitMqName): QueueItem
    {
        $this->rabbitMqName = $rabbitMqName;
        return $this;
    }

    /**
     * @param string $connection
     * @return QueueItem
     */
    public function setConnection(string $connection): QueueItem
    {
        $this->connection = $connection;
        return $this;
    }

    /**
     * @param QueueItemAttributes $attributes
     * @return QueueItem
     */
    public function setAttributes(QueueItemAttributes $attributes): QueueItem
    {
        $this->attributes = $attributes;
        return $this;
    }
}
