<?php

namespace SMSkin\ServiceBus\Support\Supervisor\Events;

use SMSkin\ServiceBus\Support\Supervisor\Supervisor;

class SupervisorLooped
{
    /**
     * The supervisor instance.
     */
    public Supervisor $supervisor;

    /**
     * Create a new event instance.
     *
     * @param Supervisor $supervisor
     */
    public function __construct(Supervisor $supervisor)
    {
        $this->supervisor = $supervisor;
    }
}
