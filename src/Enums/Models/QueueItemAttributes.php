<?php

namespace SMSkin\ServiceBus\Enums\Models;

use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Support\Collection;

class QueueItemAttributes implements Arrayable
{
    public bool $passive;
    public bool $durable;
    public bool $autoDelete;
    public bool $internal;
    public bool $nowait;
    public bool $exclusive;

    /**
     * @var Collection<QueueItemBindAttribute>
     */
    public Collection $bind;

    public function toArray(): array
    {
        return [
            'passive' => $this->passive,
            'durable' => $this->durable,
            'auto_delete' => $this->autoDelete,
            'internal' => $this->internal,
            'nowait' => $this->nowait,
            'exclusive' => $this->exclusive,
            'bind' => $this->bind->toArray()
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
}
