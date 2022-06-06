<?php

namespace SMSkin\ServiceBus\Support\Supervisor;

class ConsumerOptions
{
    /**
     * @param string $name Consumer name
     * @param int $time Max execution time
     * @param int $messages Messages count
     * @param int $memory Memory limit
     */
    public function __construct(public string $name, public int $time = 60, public int $messages = 100, public int $memory = 64)
    {

    }
}
