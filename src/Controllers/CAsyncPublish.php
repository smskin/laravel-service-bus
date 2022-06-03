<?php

namespace SMSkin\ServiceBus\Controllers;

use SMSkin\ServiceBus\Requests\AsyncPublishRequest;
use SMSkin\ServiceBus\Support\NeedleProject\LaravelRabbitMq\PublisherInterface;
use SMSkin\LaravelSupport\BaseController;
use SMSkin\LaravelSupport\BaseRequest;

class CAsyncPublish extends BaseController
{
    protected AsyncPublishRequest|BaseRequest|null $request;

    protected ?string $requestClass = AsyncPublishRequest::class;

    public function execute(): static
    {
        $this->getPublisher()->publish(
            json_encode($this->request->package->toArray()),
            $this->request->routingKey
        );
        return $this;
    }

    private function getPublisher(): PublisherInterface
    {
        return app(PublisherInterface::class, [
            $this->request->publisher
        ]);
    }
}
