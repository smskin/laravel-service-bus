<?php

namespace SMSkin\ServiceBus\Support\Supervisor\Console;

use SMSkin\ServiceBus\Support\Supervisor\ConsumerOptions;
use SMSkin\ServiceBus\Support\Supervisor\Supervisor;
use SMSkin\ServiceBus\Support\Supervisor\SupervisorOptions;
use Illuminate\Support\Collection;
use SMSkin\LaravelSupport\BaseCommand;
use Symfony\Component\Console\Command\SignalableCommandInterface;
use Symfony\Component\Process\Exception\ExceptionInterface;

class SupervisorCommand extends BaseCommand implements SignalableCommandInterface
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'service-bus:supervisor';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Start service bus consumer';

    protected bool $run = true;

    protected array $out = [];

    protected Collection $processes;

    protected Supervisor $supervisor;

    public function handle(): int
    {
        $config = $this->getSupervisorConfig();
        $this->startSupervisor($config);
        return -1;
    }

    private function startSupervisor(SupervisorOptions $options)
    {
        $this->supervisor = $supervisor = (new Supervisor($options))
            ->handleOutputUsing(function ($type, $line) {
                $this->info($line);
            });
        $supervisor->start();
    }

    /**
     * Get the list of signals handled by the command.
     *
     * @return array
     */
    public function getSubscribedSignals(): array
    {
        return [SIGINT, SIGTERM];
    }

    /**
     * Handle an incoming signal.
     *
     * @throws ExceptionInterface
     */
    public function handleSignal(int $signal): void
    {
        if ($signal === SIGINT) {
            $this->supervisor->terminate($signal);
        }
    }

    private function getSupervisorConfig(): SupervisorOptions
    {
        $config = config('service-bus.supervisor');

        $consumers = [];
        foreach ($config['consumers'] as $consumer) {
            $consumers[] = new ConsumerOptions(
                $consumer['name'],
                $consumer['time'],
                $consumer['messages'],
                $consumer['memory']
            );
        }

        return new SupervisorOptions(
            $consumers,
            $config['nice'],
            $config['scale'],
            $config['timeout']
        );
    }
}
