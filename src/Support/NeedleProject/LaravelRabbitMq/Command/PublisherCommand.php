<?php

namespace SMSkin\ServiceBus\Support\NeedleProject\LaravelRabbitMq\Command;

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

    private ?Container $container = null;

    /**
     * BasePublisherCommand constructor.
     *
     * @param Container $container
     */
    public function __construct(Container $container)
    {
        $this->container = $container;
        parent::__construct();
    }

    /**
     * @param string $publisherAliasName
     * @return PublisherInterface|\NeedleProject\LaravelRabbitMq\PublisherInterface
     */
    protected function getPublisher(string $publisherAliasName): PublisherInterface|\NeedleProject\LaravelRabbitMq\PublisherInterface
    {
        return $this->container->getPublisher($publisherAliasName);
    }

    /**
     * Execute the console command.
     * @return int
     */
    public function handle(): int
    {
        $this->getPublisher($this->input->getArgument('publisher'))
            ->publish($this->input->getArgument('message'), $this->input->getArgument('routingKey') ?? '*');
        return 0;
    }
}
