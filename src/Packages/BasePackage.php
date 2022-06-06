<?php

namespace SMSkin\ServiceBus\Packages;

use Exception;
use SMSkin\ServiceBus\Contracts\Arrayable;
use SMSkin\ServiceBus\Packages\Messages\BaseMessage;
use Carbon\Carbon;
use SMSkin\ServiceBus\Packages\Processors\BaseProcessor;

abstract class BasePackage implements Arrayable
{
    protected ?string $package = null;
    protected string $correlationId;
    protected BaseMessage $message;
    protected Carbon $sentTime;

    abstract protected function getMessageClass(): string;

    /**
     * @return string
     * @throws Exception
     */
    public function getProcessorClass(): string
    {
        throw new Exception('Not processable package');
    }

    /**
     * @return BaseProcessor
     * @throws Exception
     */
    public function getProcessor(): BaseProcessor
    {
        $processor = $this->getProcessorClass();
        if (!$processor)
        {
            throw new Exception('Not processable package');
        }
        return new $processor($this);
    }

    public function toArray(): array
    {
        return [
            'messageType' => [
                'urn:message:' . $this->package ?? static::class
            ],
            'message' => $this->message->toArray(),
            'sentTime' => $this->sentTime->toISOString(),
            'correlationId' => $this->correlationId
        ];
    }

    public function fromArray(array $data): static
    {
        $this->package = $this->preparePackage($data);
        $this->correlationId = $data['correlationId'];
        $this->message = $this->createMessageContext()->fromArray($data['message']);
        $this->sentTime = Carbon::make($data['sentTime']);
        return $this;
    }

    private function preparePackage(array $data): string
    {
        return str_replace('urn:message:', '', $data['messageType'][0]);
    }

    private function createMessageContext(): BaseMessage
    {
        $class = $this->getMessageClass();
        return new $class;
    }

    /**
     * @param string|null $package
     * @return static
     */
    public function setPackage(?string $package): static
    {
        $this->package = $package;
        return $this;
    }

    /**
     * @param string $correlationId
     * @return static
     */
    public function setCorrelationId(string $correlationId): static
    {
        $this->correlationId = $correlationId;
        return $this;
    }

    /**
     * @param BaseMessage $message
     * @return static
     */
    public function setMessage(BaseMessage $message): static
    {
        $this->message = $message;
        return $this;
    }

    /**
     * @param Carbon $sentTime
     * @return static
     */
    public function setSentTime(Carbon $sentTime): static
    {
        $this->sentTime = $sentTime;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getPackage(): ?string
    {
        return $this->package;
    }

    /**
     * @return string
     */
    public function getCorrelationId(): string
    {
        return $this->correlationId;
    }

    /**
     * @return Carbon
     */
    public function getSentTime(): Carbon
    {
        return $this->sentTime;
    }

    /**
     * @return BaseMessage
     */
    public function getMessage(): BaseMessage
    {
        return $this->message;
    }
}
