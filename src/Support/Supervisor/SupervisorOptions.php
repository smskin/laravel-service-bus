<?php

namespace SMSkin\ServiceBus\Support\Supervisor;

class SupervisorOptions
{
    protected string $command = 'service-bus:consume';

    /**
     * @param ConsumerOptions[] Consumers
     * @param int $nice The process priority.
     * @param int $scale Initial scale
     * @param int $timeout The maximum number of seconds a child worker may run.
     */
    public function __construct(public array $consumers, public int $nice = 0, public int $scale = 1, public int $timeout = 60)
    {

    }

    public function toWorkerCommand(ConsumerOptions $options): string
    {
        $escape = '\\' === DIRECTORY_SEPARATOR ? '"' : '\'';
        $command = 'exec ' . $escape . PHP_BINARY . $escape . ' artisan ' . $this->command;

        return sprintf(
            "%s %s %s",
            $command,
            $options->name,
            implode(' ', [
                '--time=' . $options->time,
                '--messages=' . $options->messages,
                '--memory=' . $options->memory
            ])
        );
    }
}
