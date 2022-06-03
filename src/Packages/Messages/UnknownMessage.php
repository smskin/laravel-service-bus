<?php

namespace SMSkin\ServiceBus\Packages\Messages;

class UnknownMessage extends BaseMessage
{
    public array $data;

    public function fromArray(array $data): static
    {
        $this->data = $data;
        return $this;
    }

    public function toArray(): array
    {
        return [
            'data' => $this->data
        ];
    }
}
