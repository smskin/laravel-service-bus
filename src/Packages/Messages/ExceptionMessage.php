<?php

namespace SMSkin\ServiceBus\Packages\Messages;

use SMSkin\ServiceBus\Packages\Messages\Models\Exception;

class ExceptionMessage extends BaseMessage
{
    public string $message;
    public Exception $exception;

    public function toArray(): array
    {
        return [
            'message' => $this->message,
            'exception' => $this->exception->toArray()
        ];
    }

    public function fromArray(array $data): static
    {
        $this->message = $data['message'];
        $this->exception = (new Exception())->fromArray($data['exception']);

        return $this;
    }

    public function setMessage(string $message): static
    {
        $this->message = $message;
        return $this;
    }

    public function setException(Exception $exception): static
    {
        $this->exception = $exception;
        return $this;
    }
}