<?php

namespace SMSkin\ServiceBus\Controllers;

use PhpAmqpLib\Exception\AMQPChannelClosedException;
use PhpAmqpLib\Exception\AMQPHeartbeatMissedException;
use SMSkin\ServiceBus\Events\EPackageSubmitted;
use SMSkin\ServiceBus\Requests\AsyncPublishRequest;
use SMSkin\ServiceBus\Support\NeedleProject\LaravelRabbitMq\PublisherInterface;
use SMSkin\LaravelSupport\BaseController;
use SMSkin\LaravelSupport\BaseRequest;

class CAsyncPublish extends BaseController
{
    protected AsyncPublishRequest|BaseRequest|null $request;

    protected ?string $requestClass = AsyncPublishRequest::class;

    /**
     * @return $this
     * @throws AMQPHeartbeatMissedException
     * @throws AMQPChannelClosedException
     */
    public function execute(): static
    {
        $this->getPublisher()->publish(
            json_encode($this->request->getPackage()->toArray()),
            $this->request->getRoutingKey(),
            $this->request->getProperties()
        );
        $this->registerSubmittedEvent();
        return $this;
    }

    private function getPublisher(): PublisherInterface
    {
        return app(PublisherInterface::class, [
            $this->request->getPublisher()
        ]);
    }

    private function registerSubmittedEvent()
    {
        event(new EPackageSubmitted(
            $this->request->getPackage(),
            $this->request->getPublisher(),
        ));
    }
}
