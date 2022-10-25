<?php

namespace SMSkin\ServiceBus\Support\NeedleProject\LaravelRabbitMq\Providers;

use RuntimeException;
use SMSkin\ServiceBus\Enums\Models\ConsumerItem;
use SMSkin\ServiceBus\Enums\Models\ExchangeItem;
use SMSkin\ServiceBus\Enums\Models\PublisherItem;
use SMSkin\ServiceBus\Enums\Models\QueueItem;
use SMSkin\ServiceBus\Enums\Models\QueueItemBindAttribute;
use SMSkin\ServiceBus\Support\ConsumerMessageProcessor;
use SMSkin\ServiceBus\Support\NeedleProject\LaravelRabbitMq\Builder\ContainerBuilder;
use SMSkin\ServiceBus\Support\NeedleProject\LaravelRabbitMq\Command\ConsumerCommand;
use SMSkin\ServiceBus\Support\NeedleProject\LaravelRabbitMq\Command\PublisherCommand;
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
    public function register(): void
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
    public function boot(): void
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
            static function () use ($config) {
                $container = new ContainerBuilder();
                return $container->createContainer($config);
            }
        );
    }

    private function getConfig(): array
    {
        $config = config('service-bus');
        $exchanges = $this->getExchanges($config['exchanges']);
        $queues = $this->getQueues($config['queues']);
        $publishers = $this->getPublishers($config['publishers']);
        $consumers = $this->getConsumers($config['consumers']);
        return [
            'connections' => $config['connections']['async'],
            'exchanges' => $this->getExchangesConfig($exchanges),
            'queues' => $this->getQueuesConfig($queues),
            'publishers' => $this->getPublishersConfig($publishers),
            'consumers' => $this->getConsumersConfig($consumers)
        ];
    }

    /**
     * @param array $config
     * @return Collection<ExchangeItem>
     */
    private function getExchanges(array $config): Collection
    {
        return self::getExchangesEnum()::items()->filter(static function (ExchangeItem $item) use ($config) {
            return in_array($item->id, $config);
        });
    }

    /**
     * @param Collection<ExchangeItem> $exchanges
     * @return array
     */
    private function getExchangesConfig(Collection $exchanges): array
    {
        $data = [];
        foreach ($exchanges as $exchange) {
            $data = array_merge($data, [
                $exchange->id => [
                    'connection' => $exchange->connection,
                    'name' => $exchange->rabbitMqName,
                    'attributes' => $exchange->attributes->toArray()
                ],
                $exchange->id . '_error' => [
                    'connection' => $exchange->connection,
                    'name' => $exchange->rabbitMqName . '_error',
                    'attributes' => $exchange->attributes->toArray()
                ]
            ]);
        }
        return $data;
    }

    /**
     * @param array $config
     * @return Collection<QueueItemBindAttribute>
     */
    private function getQueues(array $config): Collection
    {
        return self::getQueuesEnum()::items()->filter(static function (QueueItem $item) use ($config) {
            return in_array($item->id, $config);
        });
    }

    /**
     * @param Collection<QueueItem> $queues
     * @return array
     */
    private function getQueuesConfig(Collection $queues): array
    {
        $data = [];
        foreach ($queues as $queue)
        {
            $data[$queue->id] = [
                'connection' => $queue->connection,
                'name' => $queue->rabbitMqName,
                'attributes' => $queue->attributes->toArray()
            ];
            $data[$queue->id.'_error'] = [
                'connection' => $queue->connection,
                'name' => $queue->rabbitMqName . '_error',
                'attributes' => [
                    'passive' => false,
                    'durable'=>true,
                    'auto_delete'=>false,
                    'internal'=>false,
                    'nowait'=>false,
                    'exclusive'=>false,
                    'bind'=>[
                        [
                            'exchange'=> $data[$queue->id]['attributes']['bind'][0]['exchange'].'_error',
                            'routing_key' => '*'
                        ]
                    ],
                    'arguments'=>[]
                ]
            ];
        }
        return $data;
    }

    /**
     * @param array $config
     * @return Collection<PublisherItem>
     */
    private function getPublishers(array $config): Collection
    {
        return self::getPublishersEnum()::items()->filter(static function (PublisherItem $item) use ($config) {
            return in_array($item->id, $config);
        });
    }

    /**
     * @param Collection<PublisherItem> $publishers
     * @return array
     */
    private function getPublishersConfig(Collection $publishers): array
    {
        $data = [];
        foreach ($publishers as $publisher) {
            $data = array_merge($data, [
                $publisher->id => $publisher->exchange,
                $publisher->id . '_error' => $publisher->exchange . '_error'
            ]);
        }
        return $data;
    }

    /**
     * @param array $config
     * @return Collection<ConsumerItem>
     */
    private function getConsumers(array $config): Collection
    {
        return self::getConsumersEnum()::items()->filter(static function (ConsumerItem $item) use ($config) {
            return in_array($item->id, $config);
        });
    }

    /**
     * @param Collection<ConsumerItem> $consumers
     * @return array
     */
    private function getConsumersConfig(Collection $consumers): array
    {
        $data = [];
        foreach ($consumers as $consumer) {
            $data = array_merge($data, [
                $consumer->id => [
                    'queue' => $consumer->queue,
                    'prefetch_count' => $consumer->prefetchCount,
                    'message_processor' => ConsumerMessageProcessor::class
                ]
            ],
                [
                    $consumer->id . '_error' => [
                        'queue' => $consumer->queue . '_error',
                        'prefetch_count' => $consumer->prefetchCount,
                        'message_processor' => ConsumerMessageProcessor::class
                    ]
                ]);
        }
        return $data;
    }

    /**
     * Register publisher bindings
     */
    private function registerPublishers()
    {
        // Get "tagged" like Publisher
        $this->app->singleton(PublisherInterface::class, static function ($application, $arguments) {
            /** @var Container $container */
            $container = $application->make(Container::class);
            if (empty($arguments)) {
                throw new RuntimeException("Cannot make Publisher. No publisher identifier provided!");
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
        $this->app->singleton(ConsumerInterface::class, static function ($application, $arguments) {
            /** @var Container $container */
            $container = $application->make(Container::class);
            if (empty($arguments)) {
                throw new RuntimeException("Cannot make Consumer. No consumer identifier provided!");
            }
            $aliasName = $arguments[0];

            if (!$container->hasConsumer($aliasName)) {
                throw new RuntimeException('Cannot make Consumer.\nNo consumer with alias name '.$aliasName.' found!');
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
            ConsumerCommand::class,
            DeleteAllCommand::class,
            PublisherCommand::class
        ]);
    }
}
