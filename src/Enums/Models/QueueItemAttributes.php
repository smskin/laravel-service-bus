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

    public array $arguments = [];

    public function toArray(): array
    {
        return [
            'passive' => $this->passive,
            'durable' => $this->durable,
            'auto_delete' => $this->autoDelete,
            'internal' => $this->internal,
            'nowait' => $this->nowait,
            'exclusive' => $this->exclusive,
            'bind' => $this->bind->toArray(),
            'arguments' => $this->arguments
        ];
    }

    public function setPassive(bool $passive): self
    {
        $this->passive = $passive;
        return $this;
    }

    public function setDurable(bool $durable): self
    {
        $this->durable = $durable;
        return $this;
    }

    public function setAutoDelete(bool $autoDelete): self
    {
        $this->autoDelete = $autoDelete;
        return $this;
    }

    public function setInternal(bool $internal): self
    {
        $this->internal = $internal;
        return $this;
    }

    public function setNowait(bool $nowait): self
    {
        $this->nowait = $nowait;
        return $this;
    }

    public function setExclusive(bool $exclusive): self
    {
        $this->exclusive = $exclusive;
        return $this;
    }

    public function setBind(Collection $bind): self
    {
        $this->bind = $bind;
        return $this;
    }

    public function setArguments(array $arguments): self
    {
        $this->arguments = $arguments;
        return $this;
    }
}
