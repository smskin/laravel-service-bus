<?php

namespace SMSkin\ServiceBus\Support\Supervisor;

use Carbon\CarbonImmutable;
use Closure;
use Countable;
use Illuminate\Support\Collection;
use Symfony\Component\Process\Exception\ExceptionInterface;
use Symfony\Component\Process\Process;

class ProcessPool implements Countable
{
    /**
     * All the active processes.
     */
    public array $processes = [];

    /**
     * The processes that are terminating.
     */
    public array $terminatingProcesses = [];

    /**
     * Indicates if the process pool is currently running.
     */
    public bool $working = true;

    /**
     * The output handler.
     *
     * @var Closure|null
     */
    public ?Closure $output;

    /**
     * Create a new process pool instance.
     *
     * @param Closure|null $output
     * @return void
     */
    public function __construct(protected SupervisorOptions $options, protected ConsumerOptions $consumerOptions, Closure $output = null)
    {
        $this->output = $output ?: function () {
            //
        };
    }

    /**
     * Scale the process count.
     *
     * @param int $processes
     * @return void
     */
    public function scale(int $processes)
    {
        $processes = max(0, $processes);

        if ($processes === count($this->processes)) {
            return;
        }

        if ($processes > count($this->processes)) {
            $this->scaleUp($processes);
        } else {
            $this->scaleDown($processes);
        }
    }

    /**
     * Scale up to the given number of processes.
     *
     * @param int $processes
     * @return void
     */
    protected function scaleUp(int $processes)
    {
        $difference = $processes - count($this->processes);

        for ($i = 0; $i < $difference; $i++) {
            $this->start();
        }
    }

    /**
     * Scale down to the given number of processes.
     *
     * @param int $processes
     * @return void
     */
    protected function scaleDown(int $processes)
    {
        $difference = count($this->processes) - $processes;

        // Here we will slice off the correct number of processes that we need to terminate
        // and remove them from the active process array. We'll be adding them the array
        // of terminating processes where they'll run until they are fully terminated.
        $terminatingProcesses = array_slice(
            $this->processes, 0, $difference
        );

        collect($terminatingProcesses)->each(function ($process) {
            $this->markForTermination($process);
        })->all();

        $this->removeProcesses($difference);

        // Finally, we will call the terminate method on each of the processes that need get
        // terminated so they can start terminating. Terminating is a graceful operation
        // so any jobs they are already running will finish running before these quit.
        collect($this->terminatingProcesses)
            ->each(function ($process) {
                $process['process']->terminate();
            });
    }

    /**
     * Mark the given worker process for termination.
     *
     * @param WorkerProcess $process
     * @return void
     */
    public function markForTermination(WorkerProcess $process)
    {
        $this->terminatingProcesses[] = [
            'process' => $process, 'terminatedAt' => CarbonImmutable::now(),
        ];
    }

    /**
     * Remove the given number of processes from the process array.
     *
     * @param int $count
     * @return void
     */
    protected function removeProcesses(int $count)
    {
        array_splice($this->processes, 0, $count);

        $this->processes = array_values($this->processes);
    }

    /**
     * Add a new worker process to the pool.
     *
     * @return ProcessPool
     */
    protected function start(): static
    {
        $this->processes[] = $this->createProcess()->handleOutputUsing(function ($type, $line) {
            call_user_func($this->output, $type, $line);
        });

        return $this;
    }

    /**
     * Create a new process instance.
     *
     * @return WorkerProcess
     */
    protected function createProcess(): WorkerProcess
    {
        return new WorkerProcess(Process::fromShellCommandline(
            $this->options->toWorkerCommand($this->consumerOptions)
        )->setTimeout(null)->disableOutput());
    }

    /**
     * Evaluate the current state of all the processes.
     *
     * @return void
     */
    public function monitor()
    {
        $this->processes()->each(function (WorkerProcess $process) {
            $process->monitor();
        });
    }

    /**
     * Terminate all current workers and start fresh ones.
     *
     * @return void
     */
    public function restart()
    {
        $count = count($this->processes);

        $this->scale(0);

        $this->scale($count);
    }

    /**
     * Pause all the worker processes.
     *
     * @return void
     * @throws ExceptionInterface
     */
    public function pause()
    {
        $this->working = false;

        $this->processes()->each(function (WorkerProcess $process) {
            $process->pause();
        });
    }

    /**
     * Instruct all the worker processes to continue working.
     *
     * @return void
     * @throws ExceptionInterface
     */
    public function continue()
    {
        $this->working = true;

        $this->processes()->each(function (WorkerProcess $process) {
            $process->continue();
        });
    }

    /**
     * @return void
     * @throws ExceptionInterface
     */
    public function terminate()
    {
        $this->working = false;
        $this->processes()->each(function (WorkerProcess $process) {
            $process->terminate();
        });
    }

    /**
     * Get the processes that are still terminating.
     *
     * @return Collection
     */
    public function terminatingProcesses(): Collection
    {
        $this->pruneTerminatingProcesses();

        return collect($this->terminatingProcesses);
    }

    /**
     * Remove any non-running processes from the terminating process list.
     *
     * @return void
     */
    public function pruneTerminatingProcesses()
    {
        $this->stopTerminatingProcessesThatAreHanging();

        $this->terminatingProcesses = collect(
            $this->terminatingProcesses
        )->filter(function ($process) {
            return $process['process']->isRunning();
        })->all();
    }

    /**
     * Stop any terminating processes that are hanging too long.
     *
     * @return void
     */
    protected function stopTerminatingProcessesThatAreHanging()
    {
        foreach ($this->terminatingProcesses as $process) {
            $timeout = $this->options->timeout;

            if ($process['terminatedAt']->addSeconds($timeout)->lte(CarbonImmutable::now())) {
                $process['process']->stop();
            }
        }
    }

    /**
     * Get all the current processes as a collection.
     *
     * @return Collection<WorkerProcess>
     */
    public function processes(): Collection
    {
        return collect($this->processes);
    }

    /**
     * Get all the current running processes as a collection.
     *
     * @return Collection
     */
    public function runningProcesses(): Collection
    {
        return $this->processes()->filter(function (WorkerProcess $process) {
            return $process->process->isRunning();
        });
    }

    /**
     * Get the total active process count, including processes pending termination.
     *
     * @return int
     */
    public function totalProcessCount(): int
    {
        return count($this->processes()) + count($this->terminatingProcesses);
    }

    /**
     * Count the total number of processes in the pool.
     *
     * @return int
     */
    public function count(): int
    {
        return count($this->processes);
    }
}
