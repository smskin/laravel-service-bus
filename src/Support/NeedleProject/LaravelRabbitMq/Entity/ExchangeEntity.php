<?php

namespace SMSkin\ServiceBus\Support\NeedleProject\LaravelRabbitMq\Entity;

use NeedleProject\LaravelRabbitMq\Entity\AMQPEntityInterface;
use PhpAmqpLib\Exception\AMQPChannelClosedException;
use PhpAmqpLib\Exception\AMQPProtocolChannelException;
use PhpAmqpLib\Message\AMQPMessage;
use SMSkin\ServiceBus\Support\NeedleProject\LaravelRabbitMq\PublisherInterface;

class ExchangeEntity extends \NeedleProject\LaravelRabbitMq\Entity\ExchangeEntity implements PublisherInterface, AMQPEntityInterface
{
    /**
     * @const int   Retry count when a Channel Closed exeption is thrown
     */
    const MAX_RETRIES = 3;

    /**
     * @throws AMQPProtocolChannelException
     */
    public function bind()
    {
        if (!isset($this->attributes['bind']) || empty($this->attributes['bind'])) {
            return;
        }
        foreach ($this->attributes['bind'] as $bindItem) {
            try {
                $this->getChannel()
                    ->queue_bind(
                        $bindItem['queue'],
                        $this->attributes['name'],
                        $bindItem['routing_key'],
                        $this->attributes['nowait'],
                        $this->attributes['arguments'],
                    );
            } catch (AMQPProtocolChannelException $e) {
                // 404 is the code for trying to bind to an non-existing entity
                if (true === $this->attributes['throw_exception_on_bind_fail'] || $e->amqp_reply_code !== 404) {
                    throw $e;
                }
                $this->getConnection()->reconnect();
            }
        }
    }

    /**
     * Publish a message
     *
     * @param string $message
     * @param string $routingKey
     * @param array $properties
     * @return mixed|void
     * @throws AMQPProtocolChannelException
     */
    public function publish(string $message, string $routingKey = '', array $properties = [])
    {
        if ($this->attributes['auto_create'] === true) {
            $this->create();
            $this->bind();
        }
        try {
            $this->getChannel()->basic_publish(
                new AMQPMessage($message, $properties),
                $this->attributes['name'],
                $routingKey,
                true
            );
            $this->retryCount = 0;
        } catch (AMQPChannelClosedException $exception) {
            $this->retryCount++;
            // Retry publishing with re-connect
            if ($this->retryCount < self::MAX_RETRIES) {
                $this->getConnection()->reconnect();
                $this->publish($message, $routingKey, $properties);
                return;
            }
            throw $exception;
        }
    }
}
