<?php

namespace SMSkin\ServiceBus\Support\NeedleProject\LaravelRabbitMq\Entity;

use NeedleProject\LaravelRabbitMq\Entity\AMQPEntityInterface;
use SMSkin\ServiceBus\Support\NeedleProject\LaravelRabbitMq\PublisherInterface;

class ExchangeEntity extends \NeedleProject\LaravelRabbitMq\Entity\ExchangeEntity implements PublisherInterface, AMQPEntityInterface
{

}
