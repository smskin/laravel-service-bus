<?php

namespace SMSkin\ServiceBus\Support;

use SMSkin\ServiceBus\Exceptions\PackageConsumerNotExists;
use SMSkin\ServiceBus\ServiceBus;
use SMSkin\ServiceBus\Requests\ConsumeRequest;
use NeedleProject\LaravelRabbitMq\Processor\AbstractMessageProcessor;
use PhpAmqpLib\Message\AMQPMessage;
use Throwable;

class ConsumerMessageProcessor extends AbstractMessageProcessor
{
    /**
     * @param AMQPMessage $message
     * @return bool
     */
    public function processMessage(AMQPMessage $message): bool
    {
        try {
            app(ServiceBus::class)->consume(
                (new ConsumeRequest)->setJson($message->getBody())
            );
            return true;
        } catch (PackageConsumerNotExists) {
            return false;
        } catch (Throwable) {
            return true;
        }
    }
}
