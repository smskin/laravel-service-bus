<?php

namespace SMSkin\ServiceBus\Support\Supervisor;

use SMSkin\ServiceBus\Support\Supervisor\Events\SupervisorLooped;
use Closure;
use Illuminate\Support\Collection;
use Symfony\Component\Process\Exception\ExceptionInterface;

class Supervisor
{
    /**
     * All the process pools being managed.
     *
     * @var Collection<ProcessPool>
     */
    public Collection $processPools;
    protected Closure $output;

    /**
     * Indicates if the Supervisor processes are working.
     *
     * @var bool
     */
    public bool $working = true;

    public function __construct(protected SupervisorOptions $options)
    {
        if ($this->options->nice) {
            proc_nice($this->options->nice);
        }

        $this->processPools = $this->createProcessPools();
        $this->output = static function () {
        };
    }

    public function start(): void
    {
        $this->reScale($this->options->scale);
        $this->monitor();
    }

    public function restart(): void
    {
        $this->working = true;
        $this->processPools->each(static function (ProcessPool $pool) {
            $pool->restart();
        });
    }

    /**
     * @return void
     * @throws ExceptionInterface
     */
    public function pause(): void
    {
        if (!$this->working) {
            return;
        }

        $this->working = false;
        $this->processPools->each(static function (ProcessPool $pool) {
            $pool->pause();
        });
    }

    /**
     * @return void
     * @throws ExceptionInterface
     */
    public function continue(): void
    {
        if ($this->working) {
            return;
        }
        $this->working = true;
        $this->processPools->each(static function (ProcessPool $pool) {
            $pool->continue();
        });
    }

    /**
     * @throws ExceptionInterface
     */
    public function terminate(int $status = 0): void
    {
        $this->working = false;
        $this->processPools->each(static function (ProcessPool $pool) {
            $pool->processes()->each(static function (WorkerProcess $process) {
                $process->terminate();
            });
        });

        /** @noinspection PhpUndefinedMethodInspection */
        while ($this->processPools->map->runningProcesses()->collapse()->count()) {
            sleep(1);
        }
        exit($status);
    }

    public function reScale(int $processes): void
    {
        $this->processPools->each(static function (ProcessPool $pool) use ($processes) {
            $pool->scale($processes);
        });
    }

    /**
     * Set the output handler.
     */
    public function handleOutputUsing(Closure $callback): static
    {
        $this->output = $callback;

        return $this;
    }

    public function monitor(): void
    {
        while ($this->working) {
            sleep(1);
            $this->loop();
        }
    }

    private function loop(): void
    {
        $this->processPools->each(static function (ProcessPool $pool) {
            $pool->monitor();
        });
        event(new SupervisorLooped($this));
    }

    private function createPool(ConsumerOptions $consumerOptions): ProcessPool
    {
        return new ProcessPool($this->options, $consumerOptions, function ($type, $line) {
            call_user_func($this->output, $type, $line);
        });
    }

    /**
     * @return Collection<ProcessPool>
     */
    private function createProcessPools(): Collection
    {
        $pools = [];
        foreach ($this->options->consumers as $consumer) {
            $pools[] = $this->createPool($consumer);
        }
        return collect($pools);
    }
}
