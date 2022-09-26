<?php

namespace SMSkin\ServiceBus\Support\NeedleProject\LaravelRabbitMq;

interface PublisherInterface
{
    /**
     * Publish a new message
     *
     * @param string $message
     * @param string $routingKey
     * @param array $properties
     * @return mixed|void
     */
    public function publish(string $message, string $routingKey = '', array $properties = []);
}
