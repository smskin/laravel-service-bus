<?php

namespace SMSkin\ServiceBus\Contracts;

interface Arrayable extends \Illuminate\Contracts\Support\Arrayable
{
    public function fromArray(array $data): static;
}
