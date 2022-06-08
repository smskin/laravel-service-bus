<?php

namespace SMSkin\ServiceBus\Support;

use SMSkin\ServiceBus\Enums\Packages;
use SMSkin\ServiceBus\Exceptions\PackageConsumerNotExists;
use SMSkin\ServiceBus\Packages\ExceptionPackage;
use SMSkin\ServiceBus\Packages\Messages\ExceptionMessage;
use SMSkin\ServiceBus\ServiceBus;
use SMSkin\ServiceBus\Requests\ConsumeRequest;
use NeedleProject\LaravelRabbitMq\Processor\AbstractMessageProcessor;
use PhpAmqpLib\Message\AMQPMessage;
use SMSkin\ServiceBus\Support\NeedleProject\LaravelRabbitMq\PublisherInterface;
use SMSkin\ServiceBus\Traits\ClassFromConfig;
use Throwable;
use Illuminate\Support\Str;
use SMSkin\ServiceBus\Packages\Messages\Models\Exception;

class ConsumerMessageProcessor extends AbstractMessageProcessor
{
    use ClassFromConfig;

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
        } catch (Throwable $exception) {
            $this->putPackageToErrorQueue($message, $exception);
            return true;
        }
    }

    private function putPackageToErrorQueue(AMQPMessage $message, Throwable $exception): void
    {
        $package = (new ExceptionPackage())
            ->setCorrelationId(Str::uuid()->toString())
            ->setSentTime(now())
            ->setMessage(
                (new ExceptionMessage())
                    ->setMessage($message->getBody())
                    ->setException((new Exception())->fromException($exception))
            );

        $exchange = self::getExchangesEnum()::getByRabbitMqName($message->getExchange());
        $publisher = self::getPublishersEnum()::getByExchange($exchange->id);

        $this->getPublisher($publisher->id . '_error')->publish(
            json_encode($package->toArray()),
            '*'
        );
    }

    private function getPublisher(string $publisher): PublisherInterface
    {
        return app(PublisherInterface::class, [
            $publisher
        ]);
    }
}
