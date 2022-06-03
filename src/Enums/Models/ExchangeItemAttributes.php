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

    /**
     * @param string $exchangeType
     * @return ExchangeItemAttributes
     */
    public function setExchangeType(string $exchangeType): ExchangeItemAttributes
    {
        $this->exchangeType = $exchangeType;
        return $this;
    }

    /**
     * @param bool $passive
     * @return ExchangeItemAttributes
     */
    public function setPassive(bool $passive): ExchangeItemAttributes
    {
        $this->passive = $passive;
        return $this;
    }

    /**
     * @param bool $durable
     * @return ExchangeItemAttributes
     */
    public function setDurable(bool $durable): ExchangeItemAttributes
    {
        $this->durable = $durable;
        return $this;
    }

    /**
     * @param bool $autoDelete
     * @return ExchangeItemAttributes
     */
    public function setAutoDelete(bool $autoDelete): ExchangeItemAttributes
    {
        $this->autoDelete = $autoDelete;
        return $this;
    }

    /**
     * @param bool $internal
     * @return ExchangeItemAttributes
     */
    public function setInternal(bool $internal): ExchangeItemAttributes
    {
        $this->internal = $internal;
        return $this;
    }

    /**
     * @param bool $nowait
     * @return ExchangeItemAttributes
     */
    public function setNowait(bool $nowait): ExchangeItemAttributes
    {
        $this->nowait = $nowait;
        return $this;
    }

    /**
     * @param bool $throwExceptionOnRedeclare
     * @return ExchangeItemAttributes
     */
    public function setThrowExceptionOnRedeclare(bool $throwExceptionOnRedeclare): ExchangeItemAttributes
    {
        $this->throwExceptionOnRedeclare = $throwExceptionOnRedeclare;
        return $this;
    }

    /**
     * @param bool $throwExceptionOnBindFail
     * @return ExchangeItemAttributes
     */
    public function setThrowExceptionOnBindFail(bool $throwExceptionOnBindFail): ExchangeItemAttributes
    {
        $this->throwExceptionOnBindFail = $throwExceptionOnBindFail;
        return $this;
    }
}
