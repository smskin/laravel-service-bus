<?php

namespace SMSkin\ServiceBus\Packages\Messages\Models;

use SMSkin\ServiceBus\Packages\Messages\BaseMessage;
use Throwable;

class Exception extends BaseMessage
{
    public string $exception;
    public string $message;
    public string $file;
    public int $line;
    public string $trace;

    public function toArray(): array
    {
        return [
            'exception' => $this->exception,
            'message' => $this->message,
            'file' => $this->file,
            'line' => $this->line,
            'trace' => $this->trace
        ];
    }

    public function fromArray(array $data): static
    {
        $this->exception = $data['exception'];
        $this->message = $data['message'];
        $this->file = $data['file'];
        $this->line = $data['line'];
        $this->trace = $data['trace'];
        return $this;
    }

    public function fromException(Throwable $exception): static
    {
        $this->exception = get_class($exception);
        $this->message = $exception->getMessage();
        $this->file = $exception->getFile();
        $this->line = $exception->getLine();
        $this->trace = $exception->getTraceAsString();
        return $this;
    }
}
