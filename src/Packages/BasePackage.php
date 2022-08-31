<?php

namespace SMSkin\ServiceBus\Packages;

use SMSkin\ServiceBus\Contracts\Arrayable;
use SMSkin\ServiceBus\Exceptions\NotProcessablePackage;
use SMSkin\ServiceBus\Packages\Messages\BaseMessage;
use Carbon\Carbon;
use SMSkin\ServiceBus\Packages\Processors\BaseProcessor;

abstract class BasePackage implements Arrayable
{
    protected ?string $messageType = null;
    protected ?string $messageId = null;
    protected string $correlationId;
    protected ?string $conversationId = null;
    protected ?string $initiatorId = null;
    protected ?string $sourceAddress = null;
    protected ?string $destinationAddress = null;
    protected BaseMessage $message;
    protected Carbon $sentTime;
    protected ?Host $host = null;
    protected ?int $priority = 1;

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
                'urn:message:' . $this->messageType ?? static::class
            ],
            'message' => $this->message->toArray(),
            'sentTime' => $this->sentTime->toISOString(),
            'host' => $this->host?->toArray(),
            'priority' => $this->priority
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
        $this->priority = $data['priority'];
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

    /**
     * @param string $messageId
     * @return static
     */
    public function setMessageId(string $messageId): static
    {
        $this->messageId = $messageId;
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

    public function getMessageType(): string
    {
        return $this->messageType;
    }

    /**
     * @param string|null $messageType
     * @return BasePackage
     */
    public function setMessageType(?string $messageType): BasePackage
    {
        $this->messageType = $messageType;
        return $this;
    }

    /**
     * @param int|null $priority
     * @return BasePackage
     */
    public function setPriority(?int $priority): BasePackage
    {
        $this->priority = $priority;
        return $this;
    }
}
