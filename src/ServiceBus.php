<?php

namespace SMSkin\ServiceBus;

use PhpAmqpLib\Exception\AMQPChannelClosedException;
use PhpAmqpLib\Exception\AMQPHeartbeatMissedException;
use PhpAmqpLib\Exception\AMQPProtocolChannelException;
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
     * @throws AMQPHeartbeatMissedException
     * @throws AMQPChannelClosedException
     * @throws AMQPProtocolChannelException
     */
    public function asyncPublish(AsyncPublishRequest $request): void
    {
        (new CAsyncPublish($request))->execute();
    }

    /**
     * @throws Exceptions\ApiTokenNotDefined
     * @throws Exceptions\PackageConsumerNotExists
     * @throws GuzzleException
     * @noinspection PhpUnusedPrivateMethodInspection
     */
    private function syncPublish(SyncPublishRequest $request): BasePackage|null
    {
        return (new CSyncPublish($request))->execute()->getResult();
    }

    /**
     * @throws Exceptions\PackageConsumerNotExists
     * @throws Throwable
     */
    public function consume(ConsumeRequest $request): BasePackage|null
    {
        return (new CConsume($request))->execute()->getResult();
    }
}
