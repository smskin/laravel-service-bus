<?php

namespace SMSkin\ServiceBus\Enums\Models;

use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Support\Collection;
use PhpAmqpLib\Wire\AMQPTable;

class QueueItemAttributes implements Arrayable
{
    public bool $passive;
    public bool $durable;
    public bool $autoDelete;
    public bool $internal;
    public bool $nowait;
    public bool $exclusive;
    public array $arguments = [];

    /**
     * @var Collection<QueueItemBindAttribute>
     */
    public Collection $bind;

    public function toArray(): array
    {
        $options = new AMQPTable($this->arguments);
        return [
            'passive' => $this->passive,
            'durable' => $this->durable,
            'auto_delete' => $this->autoDelete,
            'internal' => $this->internal,
            'nowait' => $this->nowait,
            'exclusive' => $this->exclusive,
            'bind' => $this->bind->toArray(),
            'arguments' => $options
        ];
    }

    /**
     * @param bool $passive
     * @return QueueItemAttributes
     */
    public function setPassive(bool $passive): QueueItemAttributes
    {
        $this->passive = $passive;
        return $this;
    }

    /**
     * @param bool $durable
     * @return QueueItemAttributes
     */
    public function setDurable(bool $durable): QueueItemAttributes
    {
        $this->durable = $durable;
        return $this;
    }

    /**
     * @param bool $autoDelete
     * @return QueueItemAttributes
     */
    public function setAutoDelete(bool $autoDelete): QueueItemAttributes
    {
        $this->autoDelete = $autoDelete;
        return $this;
    }

    /**
     * @param bool $internal
     * @return QueueItemAttributes
     */
    public function setInternal(bool $internal): QueueItemAttributes
    {
        $this->internal = $internal;
        return $this;
    }

    /**
     * @param bool $nowait
     * @return QueueItemAttributes
     */
    public function setNowait(bool $nowait): QueueItemAttributes
    {
        $this->nowait = $nowait;
        return $this;
    }

    /**
     * @param bool $exclusive
     * @return QueueItemAttributes
     */
    public function setExclusive(bool $exclusive): QueueItemAttributes
    {
        $this->exclusive = $exclusive;
        return $this;
    }

    /**
     * @param Collection<QueueItemBindAttribute> $bind
     * @return QueueItemAttributes
     */
    public function setBind(Collection $bind): QueueItemAttributes
    {
        $this->bind = $bind;
        return $this;
    }

    /**
     * @param array $arguments
     * @return QueueItemAttributes
     */
    public function setArguments(array $arguments): QueueItemAttributes
    {
        $this->arguments = $arguments;
        return $this;
    }
}
