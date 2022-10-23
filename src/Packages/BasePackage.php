<?php

namespace SMSkin\ServiceBus\Packages;

use SMSkin\ServiceBus\Contracts\Arrayable;
use SMSkin\ServiceBus\Exceptions\NotProcessablePackage;
use SMSkin\ServiceBus\Packages\Messages\BaseMessage;
use Carbon\Carbon;
use SMSkin\ServiceBus\Packages\Processors\BaseProcessor;

abstract class BasePackage implements Arrayable
{
    protected string|null $messageType = null;
    protected string|null $messageId = null;
    protected string $correlationId;
    protected string|null $conversationId = null;
    protected string|null $initiatorId = null;
    protected string|null $sourceAddress = null;
    protected string|null $destinationAddress = null;
    protected BaseMessage $message;
    protected Carbon $sentTime;
    protected Host|null $host = null;

    abstract public function package(): string;

    abstract protected function messageClass(): string;

    /**
     * @return string
     * @throws NotProcessablePackage
     */
    public function getProcessorClass(): string
    {
        throw new NotProcessablePackage();
    }

    /**
     * @return BaseProcessor
     * @throws NotProcessablePackage
     */
    public function getProcessor(): BaseProcessor
    {
        $processor = $this->getProcessorClass();
        if (!$processor) {
            throw new NotProcessablePackage();
        }
        return new $processor($this);
    }

    public function toArray(): array
    {
        return [
            'messageId' => $this->messageId,
            'correlationId' => $this->correlationId,
            'conversationId' => $this->conversationId,
            'initiatorId' => $this->initiatorId,
            'sourceAddress' => $this->sourceAddress,
            'destinationAddress' => $this->destinationAddress,
            'messageType' => [
                'urn:message:' . $this->getMessageType() ?? static::class
            ],
            'message' => $this->message->toArray(),
            'sentTime' => $this->sentTime->toISOString(),
            'host' => $this->host?->toArray()
        ];
    }

    public function fromArray(array $data): static
    {
        $this->messageType = $this->parseMessageType($data);
        $this->messageId = $data['messageId'];
        $this->correlationId = $data['correlationId'];
        $this->conversationId = $data['conversationId'];
        $this->initiatorId = $data['initiatorId'];
        $this->sourceAddress = $data['sourceAddress'];
        $this->destinationAddress = $data['destinationAddress'];
        $this->message = $this->createMessageContext()->fromArray($data['message']);
        $this->sentTime = Carbon::make($data['sentTime']);
        $this->host = $data['host'] ? (new Host())->fromArray($data['host']) : null;
        return $this;
    }

    private function parseMessageType(array $data): string
    {
        return str_replace('urn:message:', '', $data['messageType'][0]);
    }

    private function createMessageContext(): BaseMessage
    {
        $class = $this->messageClass();
        return new $class;
    }

    public function setMessageId(string $messageId): static
    {
        $this->messageId = $messageId;
        return $this;
    }

    public function setCorrelationId(string $correlationId): static
    {
        $this->correlationId = $correlationId;
        return $this;
    }

    public function setMessage(BaseMessage $message): static
    {
        $this->message = $message;
        return $this;
    }

    public function setSentTime(Carbon $sentTime): static
    {
        $this->sentTime = $sentTime;
        return $this;
    }

    public function getCorrelationId(): string
    {
        return $this->correlationId;
    }

    public function getSentTime(): Carbon
    {
        return $this->sentTime;
    }

    public function getMessage(): BaseMessage
    {
        return $this->message;
    }

    public function getMessageType(): string|null
    {
        return $this->messageType;
    }

    public function setMessageType(string|null $messageType): self
    {
        $this->messageType = $messageType;
        return $this;
    }
}
