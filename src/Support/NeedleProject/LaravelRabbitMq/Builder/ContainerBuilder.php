<?php

namespace SMSkin\ServiceBus\Support\NeedleProject\LaravelRabbitMq\Builder;

use RuntimeException;
use SMSkin\ServiceBus\Support\NeedleProject\LaravelRabbitMq\Container;
use Illuminate\Support\Collection;
use NeedleProject\LaravelRabbitMq\AMQPConnection;
use SMSkin\ServiceBus\Support\NeedleProject\LaravelRabbitMq\Entity\ExchangeEntity;
use SMSkin\ServiceBus\Support\NeedleProject\LaravelRabbitMq\Entity\QueueEntity;

/**
 * Class ContainerBuilder
 *
 * @package NeedleProject\LaravelRabbitMq\Builder
 * @author  Adrian Tilita <adrian@tilita.ro>
 * @todo    Add config validator
 */
class ContainerBuilder
{
    /**
     * Create RabbitMQ Container
     */
    public function createContainer(array $config): Container
    {
        $connections = $this->createConnections($config['connections']);
        $exchanges = $this->createExchanges($config['exchanges'], $connections);
        $queues = $this->createQueues($config['queues'], $connections);

        $container = new Container();
        // create publishers
        foreach ($config['publishers'] as $publisherAliasName => $publisherEntityBind) {
            if ($exchanges->has($publisherEntityBind)) {
                $entity = $exchanges->get($publisherEntityBind);
            } elseif ($queues->has($publisherEntityBind)) {
                $entity = $queues->get($publisherEntityBind);
            } else {
                throw new RuntimeException(
                    sprintf(
                        "Cannot create publisher %s: no exchange or queue named %s defined!",
                        $publisherAliasName,
                        $publisherEntityBind
                    )
                );
            }

            $container->addPublisher(
                $publisherAliasName,
                $entity
            );
        }

        foreach ($config['consumers'] as $consumerAliasName => $consumerDetails) {
            $prefetchCount    = $consumerDetails['prefetch_count'];
            $messageProcessor = $consumerDetails['message_processor'];

            if ($queues->has($consumerDetails['queue'])) {
                /** @var QueueEntity $entity */
                $entity = $queues->get($consumerDetails['queue']);
            } else {
                throw new RuntimeException(
                    sprintf(
                        "Cannot create consumer %s: no queue named %s defined!",
                        $consumerAliasName,
                        $consumerDetails['queue']
                    )
                );
            }

            $entity->setPrefetchCount($prefetchCount);
            $entity->setMessageProcessor($messageProcessor);
            $container->addConsumer($consumerAliasName, $entity);
        }

        return $container;
    }

    /**
     * Create connections
     */
    private function createConnections(array $connectionConfig): Collection
    {
        $connections = new Collection();
        foreach ($connectionConfig as $connectionAliasName => $connectionCredentials) {
            $connections->put(
                $connectionAliasName,
                AMQPConnection::createConnection($connectionAliasName, $connectionCredentials)
            );
        }
        return $connections;
    }

    private function createExchanges(array $exchangeConfigList, Collection $connections): Collection
    {
        $exchanges = new Collection();
        foreach ($exchangeConfigList as $exchangeAliasName => $exchangeDetails) {
            // verify if the connection exists
            if (array_key_exists('connection', $exchangeDetails) &&
                $connections->has($exchangeDetails['connection']) === false) {
                throw new RuntimeException(
                    sprintf(
                        "Could not create exchange %s: connection name %s is not defined!",
                        $exchangeAliasName,
                        $exchangeDetails['connection']
                    )
                );
            }

            $exchanges->put(
                $exchangeAliasName,
                ExchangeEntity::createExchange(
                    $connections->get($exchangeDetails['connection']),
                    $exchangeAliasName,
                    array_merge($exchangeDetails['attributes'], ['name' => $exchangeDetails['name']])
                )
            );
        }
        return $exchanges;
    }

    private function createQueues(array $queueConfigList, Collection $connections): Collection
    {
        $queue = new Collection();
        foreach ($queueConfigList as $queueAliasName => $queueDetails) {
            // verify if the connection exists
            if (array_key_exists('connection', $queueDetails) &&
                $connections->has($queueDetails['connection']) === false) {
                throw new RuntimeException(
                    sprintf(
                        "Could not create exchange %s: connection name %s is not defined!",
                        $queueAliasName,
                        $queueDetails['connection']
                    )
                );
            }

            $queue->put(
                $queueAliasName,
                QueueEntity::createQueue(
                    $connections->get($queueDetails['connection']),
                    $queueAliasName,
                    array_merge($queueDetails['attributes'], ['name' => $queueDetails['name']])
                )
            );
        }
        return $queue;
    }
}
