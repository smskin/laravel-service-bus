<?php

namespace SMSkin\ServiceBus\Support\Supervisor\Events;

use SMSkin\ServiceBus\Support\Supervisor\WorkerProcess;

class WorkerProcessRestarting
{
    /**
     * The worker process instance.
     */
    public WorkerProcess $process;

    /**
     * Create a new event instance.
     */
    public function __construct(WorkerProcess $process)
    {
        $this->process = $process;
    }
}
