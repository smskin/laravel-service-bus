<?php

namespace SMSkin\ServiceBus\Enums\Models;

use Illuminate\Contracts\Support\Arrayable;

class ExchangeItemAttributes implements Arrayable
{
    public string $exchangeType = 'topic';
    public bool $passive = false;
    public bool $durable = false;
    public bool $autoDelete = false;
    public bool $internal = false;
    public bool $nowait = false;
    public bool $throwExceptionOnRedeclare = true;
    public bool $throwExceptionOnBindFail = true;

    public function toArray(): array
    {
        return [
            'exchange_type' => $this->exchangeType,
            'passive' => $this->passive,
            'durable' => $this->durable,
            'auto_delete' => $this->autoDelete,
            'internal' => $this->internal,
            'nowait' => $this->nowait,
            'throw_exception_on_redeclare' => $this->throwExceptionOnRedeclare,
            'throw_exception_on_bind_fail' => $this->throwExceptionOnBindFail,
        ];
    }

    public function setExchangeType(string $exchangeType): self
    {
        $this->exchangeType = $exchangeType;
        return $this;
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

    public function setThrowExceptionOnRedeclare(bool $throwExceptionOnRedeclare): self
    {
        $this->throwExceptionOnRedeclare = $throwExceptionOnRedeclare;
        return $this;
    }

    public function setThrowExceptionOnBindFail(bool $throwExceptionOnBindFail): self
    {
        $this->throwExceptionOnBindFail = $throwExceptionOnBindFail;
        return $this;
    }
}
