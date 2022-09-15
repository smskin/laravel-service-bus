<?php

namespace SMSkin\ServiceBus;

use PhpAmqpLib\Exception\AMQPChannelClosedException;
use PhpAmqpLib\Exception\AMQPHeartbeatMissedException;
use SMSkin\ServiceBus\Controllers\CAsyncPublish;
use SMSkin\ServiceBus\Controllers\CConsume;
use SMSkin\ServiceBus\Controllers\CSyncPublish;
use SMSkin\ServiceBus\Packages\BasePackage;
use SMSkin\ServiceBus\Requests\AsyncPublishRequest;
use SMSkin\ServiceBus\Requests\ConsumeRequest;
use SMSkin\ServiceBus\Requests\SyncPublishRequest;
use GuzzleHttp\Exception\GuzzleException;
use SMSkin\LaravelSupport\BaseModule;
use Throwable;

class ServiceBus extends BaseModule
{
    /**
     * @param AsyncPublishRequest $request
     * @return void
     * @throws AMQPHeartbeatMissedException
     * @throws AMQPChannelClosedException
     */
    public function asyncPublish(AsyncPublishRequest $request): void
    {
        (new CAsyncPublish($request))->execute();
    }

    /**
     * @param SyncPublishRequest $request
     * @return BasePackage|null
     * @throws Exceptions\ApiTokenNotDefined
     * @throws Exceptions\PackageConsumerNotExists
     * @throws GuzzleException
     */
    public function syncPublish(SyncPublishRequest $request): ?BasePackage
    {
        return (new CSyncPublish($request))->execute()->getResult();
    }

    /**
     * @param ConsumeRequest $request
     * @return BasePackage|null
     * @throws Exceptions\PackageConsumerNotExists
     * @throws Throwable
     */
    public function consume(ConsumeRequest $request): ?BasePackage
    {
        return (new CConsume($request))->execute()->getResult();
    }
}
