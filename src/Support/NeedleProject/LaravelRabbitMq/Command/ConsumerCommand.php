<?php

namespace SMSkin\ServiceBus\Support\NeedleProject\LaravelRabbitMq\Command;

use NeedleProject\LaravelRabbitMq\Command\BaseConsumerCommand;
use SMSkin\ServiceBus\Support\NeedleProject\LaravelRabbitMq\ConsumerInterface;
use Illuminate\Contracts\Container\BindingResolutionException;
use Symfony\Component\Console\Command\SignalableCommandInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Throwable;

/**
 * Class BaseConsumerCommand
 *
 * @package NeedleProject\LaravelRabbitMq\Command
 * @author  Adrian Tilita <adrian@tilita.ro>
 */
class ConsumerCommand extends BaseConsumerCommand implements SignalableCommandInterface
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'service-bus:consume {consumer} {--time=60} {--messages=100} {--memory=64}';

    protected ConsumerInterface $consumer;

    /**
     * Execute the console command.
     * @return int
     * @throws BindingResolutionException
     */
    public function handle(): int
    {
        $messageCount = $this->input->getOption('messages');
        $waitTime = $this->input->getOption('time');
        $memoryLimit = $this->input->getOption('memory');
        $isVerbose = in_array(
            $this->output->getVerbosity(),
            [OutputInterface::VERBOSITY_VERBOSE, OutputInterface::VERBOSITY_VERY_VERBOSE]
        );

        $this->consumer = $consumer = $this->getConsumer($this->input->getArgument('consumer'));
        if ($isVerbose) {
            try {
                $this->injectCliLogger($consumer);
            } catch (Throwable) {
                // Do nothing, we cannot inject a STDOUT logger
            }
        }
        try {
            return $consumer->startConsuming($messageCount, $waitTime, $memoryLimit);
        } catch (Throwable $e) {
            $consumer->stopConsuming();
            $this->output->error($e->getMessage());
            return -1;
        }
    }

    /**
     * @throws BindingResolutionException
     */
    protected function getConsumer(string $consumerAliasName): ConsumerInterface
    {
        return app()->make(ConsumerInterface::class, [$consumerAliasName]);
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
     */
    public function handleSignal(int $signal): void
    {
        if ($signal === SIGINT) {
            $this->consumer->stopConsuming();
        }
    }
}
