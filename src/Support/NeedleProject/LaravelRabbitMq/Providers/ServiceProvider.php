<?php

namespace SMSkin\ServiceBus\Support\NeedleProject\LaravelRabbitMq\Providers;

use SMSkin\ServiceBus\Enums\Models\ConsumerItem;
use SMSkin\ServiceBus\Enums\Models\ExchangeItem;
use SMSkin\ServiceBus\Enums\Models\PublisherItem;
use SMSkin\ServiceBus\Enums\Models\QueueItem;
use SMSkin\ServiceBus\Support\ConsumerMessageProcessor;
use SMSkin\ServiceBus\Support\NeedleProject\LaravelRabbitMq\Builder\ContainerBuilder;
use SMSkin\ServiceBus\Support\NeedleProject\LaravelRabbitMq\Command\BaseConsumerCommand;
use SMSkin\ServiceBus\Support\NeedleProject\LaravelRabbitMq\Command\BasePublisherCommand;
use SMSkin\ServiceBus\Support\NeedleProject\LaravelRabbitMq\Command\DeleteAllCommand;
use SMSkin\ServiceBus\Support\NeedleProject\LaravelRabbitMq\Command\ListEntitiesCommand;
use SMSkin\ServiceBus\Support\NeedleProject\LaravelRabbitMq\Command\SetupCommand;
use SMSkin\ServiceBus\Support\NeedleProject\LaravelRabbitMq\ConsumerInterface;
use SMSkin\ServiceBus\Support\NeedleProject\LaravelRabbitMq\Container;
use SMSkin\ServiceBus\Support\NeedleProject\LaravelRabbitMq\PublisherInterface;
use SMSkin\ServiceBus\Traits\ClassFromConfig;
use Illuminate\Support\Collection;
use Illuminate\Support\ServiceProvider as LaravelServiceProvider;
use NeedleProject\LaravelRabbitMq\ConfigHelper;
use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerInterface;

class ServiceProvider extends LaravelServiceProvider
{
    use ClassFromConfig;

    /**
     * Indicates if loading of the provider is deferred.
     */
    protected bool $defer = false;

    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->registerContainer();
        $this->registerPublishers();
        $this->registerConsumers();
    }

    /**
     * Perform post-registration booting of services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerCommands();
    }

    private function registerContainer()
    {
        $config = $this->getConfig();
        $configHelper = new ConfigHelper();
        $config = $configHelper->addDefaults($config);

        $this->app->singleton(
            Container::class,
            function () use ($config) {
                $container = new ContainerBuilder();
                return $container->createContainer($config);
            }
        );
    }

    private function getConfig(): array
    {
        $config = config('smskin.service-bus');
        $exchanges = self::getExchangesEnum()::items()->filter(function (ExchangeItem $item) use ($config) {
            return in_array($item->id, $config['exchanges']);
        });
        $queues = self::getQueuesEnum()::items()->filter(function (QueueItem $item) use ($config) {
            return in_array($item->id, $config['queues']);
        });
        $publishers = self::getPublishersEnum()::items()->filter(function (PublisherItem $item) use ($config) {
            return in_array($item->id, $config['publishers']);
        });
        $consumers = self::getConsumersEnum()::items()->filter(function (ConsumerItem $item) use ($config) {
            return in_array($item->id, $config['consumers']);
        });

        return [
            'connections' => $config['connections']['async'],
            'exchanges' => $this->getExchangesConfig($exchanges),
            'queues' => $this->getQueuesConfig($queues),
            'publishers' => $this->getPublishersConfig($publishers),
            'consumers' => $this->getConsumersConfig($consumers)
        ];
    }

    /**
     * @param Collection<ExchangeItem> $exchanges
     * @return array
     */
    private function getExchangesConfig(Collection $exchanges): array
    {
        $data = [];
        foreach ($exchanges as $exchange) {
            $data[$exchange->id] = [
                // used connection for the producer
                'connection' => $exchange->connection,
                'name' => $exchange->rabbitMqName,
                'attributes' => $exchange->attributes->toArray()
            ];
        }
        return $data;
    }

    /**
     * @param Collection<QueueItem> $queues
     * @return array
     */
    private function getQueuesConfig(Collection $queues): array
    {
        $data = [];
        foreach ($queues as $queue) {
            $data[$queue->id] = [
                // used connection for the producer
                'connection' => $queue->connection,
                'name' => $queue->rabbitMqName,
                'attributes' => $queue->attributes->toArray()
            ];
        }
        return $data;
    }

    /**
     * @param Collection<PublisherItem> $publishers
     * @return array
     */
    private function getPublishersConfig(Collection $publishers): array
    {
        $data = [];
        foreach ($publishers as $publisher) {
            $data[$publisher->id] = $publisher->exchange;
        }
        return $data;
    }

    /**
     * @param Collection<ConsumerItem> $consumers
     * @return array
     */
    private function getConsumersConfig(Collection $consumers): array
    {
        $data = [];
        foreach ($consumers as $consumer) {
            $data[$consumer->id] = [
                'queue' => $consumer->queue,
                'prefetch_count' => $consumer->prefetchCount,
                'message_processor' => ConsumerMessageProcessor::class
            ];
        }
        return $data;
    }

    /**
     * Register publisher bindings
     */
    private function registerPublishers()
    {
        // Get "tagged" like Publisher
        $this->app->singleton(PublisherInterface::class, function ($application, $arguments) {
            /** @var Container $container */
            $container = $application->make(Container::class);
            if (empty($arguments)) {
                throw new \RuntimeException("Cannot make Publisher. No publisher identifier provided!");
            }
            $aliasName = $arguments[0];
            return $container->getPublisher($aliasName);
        });
    }

    /**
     * Register consumer bindings
     */
    private function registerConsumers()
    {
        // Get "tagged" like Consumers
        $this->app->singleton(ConsumerInterface::class, function ($application, $arguments) {
            /** @var Container $container */
            $container = $application->make(Container::class);
            if (empty($arguments)) {
                throw new \RuntimeException("Cannot make Consumer. No consumer identifier provided!");
            }
            $aliasName = $arguments[0];

            if (!$container->hasConsumer($aliasName)) {
                throw new \RuntimeException("Cannot make Consumer.\nNo consumer with alias name {$aliasName} found!");
            }
            /** @var LoggerAwareInterface $consumer */
            $consumer = $container->getConsumer($aliasName);
            $consumer->setLogger($application->make(LoggerInterface::class));
            return $consumer;
        });
    }

    /**
     * Register commands
     */
    private function registerCommands()
    {
        $this->commands([
            SetupCommand::class,
            ListEntitiesCommand::class,
            BaseConsumerCommand::class,
            DeleteAllCommand::class,
            BasePublisherCommand::class
        ]);
    }
}
