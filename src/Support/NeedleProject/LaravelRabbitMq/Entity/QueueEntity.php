<?php

namespace SMSkin\ServiceBus\Support\NeedleProject\LaravelRabbitMq\Entity;

use SMSkin\ServiceBus\Support\NeedleProject\LaravelRabbitMq\PublisherInterface;
use SMSkin\ServiceBus\Support\NeedleProject\LaravelRabbitMq\ConsumerInterface;
use NeedleProject\LaravelRabbitMq\Entity\AMQPEntityInterface;
use Psr\Log\LoggerAwareInterface;

class QueueEntity extends \NeedleProject\LaravelRabbitMq\Entity\QueueEntity implements PublisherInterface, ConsumerInterface, AMQPEntityInterface, LoggerAwareInterface
{
    protected bool $working = true;

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
