<?php

namespace SMSkin\ServiceBus\Support\NeedleProject\LaravelRabbitMq;

use PhpAmqpLib\Exception\AMQPProtocolChannelException;

interface PublisherInterface extends \NeedleProject\LaravelRabbitMq\PublisherInterface
{
    /**
     * Publish a new message
     *
     * @throws AMQPProtocolChannelException
     */
    public function publish(string $message, string $routingKey = '', array $properties = []): void;
}
