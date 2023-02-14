<?php

namespace SMSkin\ServiceBus\Jobs;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Log;
use PhpAmqpLib\Exception\AMQPChannelClosedException;
use PhpAmqpLib\Exception\AMQPHeartbeatMissedException;
use PhpAmqpLib\Exception\AMQPProtocolChannelException;
use SMSkin\LaravelSupport\BaseJob;
use SMSkin\ServiceBus\Enums\Models\PublisherItem;
use SMSkin\ServiceBus\Packages\BasePackage;
use SMSkin\ServiceBus\Support\NeedleProject\LaravelRabbitMq\PublisherInterface;
use SMSkin\ServiceBus\Traits\ClassFromConfig;

class SubmitExceptionPackageToRabbitMQ extends BaseJob implements ShouldQueue
{
    use ClassFromConfig;

    public function __construct(public PublisherItem $publisher, public BasePackage $package)
    {
        parent::__construct();
    }

    /**
     * @throws AMQPProtocolChannelException
     */
    public function handle(): void
    {
        try {
            $this->getPublisher($this->publisher->id . '_error')->publish(
                json_encode($this->package->toArray(), JSON_PRETTY_PRINT),
                '*'
            );
        } catch (AMQPHeartbeatMissedException|AMQPChannelClosedException $exception) {
            Log::error($exception);
            dispatch(new self($this->publisher, $this->package))
                ->delay(now()->addSecond());
            return;
        }
    }

    private function getPublisher(string $publisher): PublisherInterface
    {
        return app(PublisherInterface::class, [
            $publisher
        ]);
    }
}