<?php

namespace SMSkin\ServiceBus\Support\NeedleProject\LaravelRabbitMq\Command;

use PhpAmqpLib\Exception\AMQPProtocolChannelException;
use SMSkin\ServiceBus\Support\NeedleProject\LaravelRabbitMq\Container;
use SMSkin\ServiceBus\Support\NeedleProject\LaravelRabbitMq\PublisherInterface;
use SMSkin\LaravelSupport\BaseCommand;

/**
 * Class BasePublisherCommand
 *
 * @package NeedleProject\LaravelRabbitMq\Command
 * @author  Adrian Tilita <adrian@tilita.ro>
 */
class PublisherCommand extends BaseCommand
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'service-bus:publish {publisher} {message} {routingKey?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Publish one message';

    /**
     * BasePublisherCommand constructor.
     */
    public function __construct(private readonly Container|null $container)
    {
        parent::__construct();
    }

    protected function getPublisher(string $publisherAliasName): PublisherInterface|\NeedleProject\LaravelRabbitMq\PublisherInterface
    {
        return $this->container->getPublisher($publisherAliasName);
    }

    /**
     * Execute the console command.
     * @return int
     * @throws AMQPProtocolChannelException
     */
    public function handle(): int
    {
        $this->getPublisher($this->input->getArgument('publisher'))
            ->publish($this->input->getArgument('message'), $this->input->getArgument('routingKey') ?? '*');
        return 0;
    }
}
