<?php

namespace SMSkin\ServiceBus\Support\NeedleProject\LaravelRabbitMq\Entity;

use PhpAmqpLib\Exception\AMQPChannelClosedException;
use PhpAmqpLib\Exception\AMQPProtocolChannelException;
use PhpAmqpLib\Message\AMQPMessage;
use SMSkin\ServiceBus\Support\NeedleProject\LaravelRabbitMq\PublisherInterface;
use SMSkin\ServiceBus\Support\NeedleProject\LaravelRabbitMq\ConsumerInterface;
use NeedleProject\LaravelRabbitMq\Entity\AMQPEntityInterface;
use Psr\Log\LoggerAwareInterface;

class QueueEntity extends \NeedleProject\LaravelRabbitMq\Entity\QueueEntity implements PublisherInterface, ConsumerInterface, AMQPEntityInterface, LoggerAwareInterface
{
    protected bool $working = true;

    /**
     * @const int   Retry count when a Channel Closed exeption is thrown
     */
    const MAX_RETRIES = 3;

    public function bind()
    {
        if (!isset($this->attributes['bind']) || empty($this->attributes['bind'])) {
            return;
        }
        foreach ($this->attributes['bind'] as $bindItem) {
            try {
                $this->getChannel()
                    ->queue_bind(
                        $this->attributes['name'],
                        $bindItem['exchange'],
                        $bindItem['routing_key'],
                        $this->attributes['nowait'],
                        $this->attributes['arguments'],
                    );
            } catch (AMQPProtocolChannelException $e) {
                // 404 is the code for trying to bind to an non-existing entity
                if (true === $this->attributes['throw_exception_on_bind_fail'] || $e->amqp_reply_code !== 404) {
                    throw $e;
                }
                $this->reconnect();
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
        \Log::alert('asdas23451', ['data' => print_r($properties, true)]);
        if ($this->attributes['auto_create'] === true) {
            $this->create();
            $this->bind();
        }

        \Log::alert('asdas23452', ['data' => print_r($properties, true)]);
        try {
            $this->getChannel()
                ->basic_publish(
                    new AMQPMessage($message. $properties),
                    '',
                    $this->attributes['name'],
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

    protected function shouldStopConsuming(): bool
    {
        if (parent::shouldStopConsuming()) {
            return true;
        }

        return !$this->working;
    }

    public function stopConsuming()
    {
        parent::stopConsuming();
        $this->working = false;
    }
}
