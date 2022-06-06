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
        $this->output = function () {
        };
    }

    public function restart()
    {
        $this->working = true;
        $this->processPools->each(function (ProcessPool $pool) {
            $pool->restart();
        });
    }

    /**
     * @return void
     * @throws ExceptionInterface
     */
    public function pause()
    {
        if (!$this->working) {
            return;
        }

        $this->working = false;
        $this->processPools->each(function (ProcessPool $pool) {
            $pool->pause();
        });
    }

    /**
     * @return void
     * @throws ExceptionInterface
     */
    public function continue()
    {
        if ($this->working) {
            return;
        }
        $this->working = true;
        $this->processPools->each(function (ProcessPool $pool) {
            $pool->continue();
        });
    }

    /**
     * @param int $status
     * @return void
     * @throws ExceptionInterface
     */
    public function terminate(int $status = 0)
    {
        echo 'Terminating' . PHP_EOL;
        $this->working = false;
        $this->processPools->each(function (ProcessPool $pool) {
            $pool->processes()->each(function (WorkerProcess $process) {
                $process->terminate();
            });
        });

        while ($this->processPools->map(function (ProcessPool $pool) {
            echo 'Running count: ' . $pool->runningProcesses()->collapse()->count() . PHP_EOL;
            return $pool->runningProcesses()->collapse()->count();
        })) {
            sleep(1);
        }
        exit($status);
    }

    public function reScale(int $processes)
    {
        $this->processPools->each(function (ProcessPool $pool) use ($processes) {
            $pool->scale($processes);
        });
    }

    /**
     * Set the output handler.
     *
     * @param Closure $callback
     * @return Supervisor
     */
    public function handleOutputUsing(Closure $callback): static
    {
        $this->output = $callback;

        return $this;
    }

    public function monitor()
    {
        while ($this->working) {
            sleep(1);
            $this->loop();
        }
    }

    private function loop()
    {
        $this->processPools->each(function (ProcessPool $pool) {
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
