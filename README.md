## Laravel service bus

This library provides communication between multiple services. Supports sync and async communications. 

This library uses:
- https://github.com/needle-project/laravel-rabbitmq - Library for supports AMPQ. Some class was overrides for support current library logic.
- https://github.com/laravel/horizon - code base of supervisor part

### Base logic

Base logic builds on package structure. This structure very similar to .Net MassTransit packages. Package contains:

- package meta
- message type
- message

Algorithm:

- Sender creates package with message
- Sender submit package to consumer
- Consumer defines requested package by message type and deserialize package
- Consumer executes logic with received package

#### Synchronous communications

- Sender creates package with message
- Sender submit package to consumer with synchronous method
    - Library serialized package to json
    - Library submits http request to consumer host
    - Consumer process some logic with received package
    - Consumer returns new package with response
- Sender receives response package from consumer

#### Asynchronous communications

Asynchronous communication provides by RabbitMQ

- Sender creates package with message
- Sender submit package to consumer with asynchronous method
    - Library serialized package to json
    - Library submits package to the RabbitMQ exchange
    - RabbitMQ submits received package to queue
- Consumer listens RabbitMQ queue
- Consumer receives package from queue
- Consumer process some logic with received package

### Installation

- `composer require smskin/laravel-service-bus`
- `php artisan vendor:publish --provider="SMSkin\ServiceBus\Providers\ServiceProvider"`
- Configure RabbitMQ connection in `service-bus.php` config file
- Run `php artisan service-bus:setup` from initialize RabbitMQ exchanges and queues
- Add service bus consumer command to the supervisor conf

Supervisor config to provides service bus consumer

```angular2html
[program:laravel-service-bus]
process_name=%(program_name)s
command=php /var/www/html/artisan service-bus:supervisor
autostart=true
autorestart=true
user=www-data
group=www-data
redirect_stderr=true
stdout_logfile=/dev/stdout
stderr_logfile=/dev/stderr
stdout_maxbytes=0
stderr_maxbytes=0
stdout_logfile_maxbytes = 0
stderr_logfile_maxbytes = 0
startsecs=0
```

### Artisan commands

- `service-bus:setup` - command for initialize RabbitMQ exchanges and queues
- `service-bus:supervisor` - service bus supervisor. Start processes for consume packages from RabbitMQ queues
- `service-bus:delete-all` - command deletes exchanges and queues from RabbitMQ
- `service-bus:list` - command lists exchanges and queues
- `service-bus:publish` - command for submit test message to RabbitMQ exchange (for tests)
- `service-bus:consume` - consumer command for receive packages from RabbitMQ queue (uses by supervisor command)

### Service bus supervisor
Supervisor command run consumer process for each registered consumer (SMSkin\ServiceBus\Enums\Consumers)

### Overriding default enums

You can create new enum and override calling it in `service-bus.php` config file. It provides extend the default
consumers and exchanges.

For example

- Create Exchanges enum
- Extends it from base SMSkin\ServiceBus\Enums\Exchanges
- Change the `items` method for provide new exchanges
- Replace default enum in `service-bus.php` config file (line 14)

### Classes

- Package - base exchange data package
- Message - package payload (DTO)
- Processor - class, that running on receive incoming package. Can return new Package in synchronous exchange mode.

#### Exchanges

Register exchanges in Exchanges enum (SMSkin\ServiceBus\Enums\Exchanges - can be overrides)

- id - internal id
- connection - id of connection (SMSkin\ServiceBus\Enums\Connections)
- rabbitMqName - RabbitMQ name of exchange
- attributes - RabbitMQ exchange attributes
    - exchangeType (exchange_type)
    - passive
    - durable
    - autoDelete (auto_delete)
    - internal
    - nowait
    - throwExceptionOnRedeclare (throw_exception_on_redeclare)
    - throwExceptionOnBindFail (throw_exception_on_bind_fail)

#### Queues

Register queues in Queues enum  (SMSkin\ServiceBus\Enums\Queues - can be overrides)

- id - internal id
- connection - id of connection (SMSkin\ServiceBus\Enums\Connections)
- rabbitMqName - RabbitMQ name of queue
- attributes
    - passive
    - durable
    - autoDelete (auto_delete)
    - internal
    - nowait
    - exclusive
    - bind - array of binds
        - exchange - id of exchange  (SMSkin\ServiceBus\Enums\Exchanges)
        - routing_key

#### Publishers

Register publishers in Publishers enum  (SMSkin\ServiceBus\Enums\Publishers - can be overrides)

- id - internal id
- exchange - id of exchange (SMSkin\ServiceBus\Enums\Exchanges)

#### Consumers

Register consumers in Consumers enum (SMSkin\ServiceBus\Enums\Consumers - can be overrides)

- id - internal id
- queue - id of queue (SMSkin\ServiceBus\Enums\Queues)
- prefetchCount

#### Hosts

Register hosts in Hosts enum (SMSkin\ServiceBus\Enums\Hosts - can be overrides)

- id - internal id
- host - url of consumer host (for provide synchronous service bus)

### Exchange package

Package must provide links to processor and message classes. Package register in Packages enum (
SMSkin\ServiceBus\Enums\Packages - can be overrides). Exchange packages has very simple structure, that support reproduce this logic in any languages.

Example of serialized package:
```json
{
    "messageId": "e5e471ae-62e3-46c5-93fd-6dd589c32748",
    "correlationId": "2712f617-4d55-4851-ad2c-5c841224e251",
    "conversationId": null,
    "initiatorId": null,
    "sourceAddress": null,
    "destinationAddress": null,
    "messageType": [
        "urn:message:TEST_ASYNC_LOCAL"
    ],
    "message": {
        "name": "Sergey"
    },
    "sentTime": "2022-06-07T10:19:15.152620Z",
    "host": null
}
```

Example of package:

```php
<?php

namespace App\Modules\ServiceBus\Packages;

use App\Modules\ServiceBus\Packages\Messages\TestMessage;
use App\Modules\ServiceBus\Packages\Processors\TestMessageProcessor;
use SMSkin\ServiceBus\Packages\BasePackage;

class TestMessagePackage extends BasePackage
{
    public function getProcessorClass(): string
    {
        return TestMessageProcessor::class;
    }

    protected function getMessageClass(): string
    {
        return TestMessage::class;
    }
}
```

Example of processor class:

```php
<?php

namespace App\Modules\ServiceBus\Packages\Processors;

use App\Modules\ServiceBus\Packages\TestMessagePackage;
use Illuminate\Support\Facades\Log;
use SMSkin\ServiceBus\Packages\BasePackage;
use SMSkin\ServiceBus\Packages\Processors\BaseProcessor;

class TestMessageProcessor extends BaseProcessor
{
    public function __construct(protected TestMessagePackage|BasePackage $package)
    {
        parent::__construct($package);
    }

    public function execute(): ?BasePackage
    {
        Log::debug('Received package', $this->package->toArray());
        return null;
    }
}
```

Example of message class:

```php
<?php

namespace App\Modules\ServiceBus\Packages\Messages;

use SMSkin\ServiceBus\Packages\Messages\BaseMessage;

class TestMessage extends BaseMessage
{
    public string $name;

    public function fromArray(array $data): static
    {
        $this->name = $data['name'];
        return $this;
    }

    public function toArray(): array
    {
        return [
            'name' => $this->name
        ];
    }

    /**
     * @param string $name
     * @return TestMessage
     */
    public function setName(string $name): TestMessage
    {
        $this->name = $name;
        return $this;
    }
}
```

Example of registration package in Packages enum

```php
<?php

namespace App\Modules\ServiceBus\Enums;

use App\Modules\ServiceBus\Packages\TestMessagePackage;
use Illuminate\Support\Collection;
use SMSkin\ServiceBus\Enums\Models\PackageItem;

class Packages extends \SMSkin\ServiceBus\Enums\Packages
{
    public const TEST_ASYNC_LOCAL = 'TEST_ASYNC_LOCAL';

    /**
     * @return Collection<PackageItem>
     */
    protected static function getItems(): Collection
    {
        return parent::getItems()->merge([
            (new PackageItem)
                ->setId(self::TEST_ASYNC_LOCAL)
                ->setClass(TestMessagePackage::class)
        ]);
    }
}
```

### Submitting 

Example of submitting synchronous command
```php
$result = (new ServiceBus)->syncPublish(
            (new SyncPublishRequest)
                ->setHost(Hosts::LOCALHOST)
                ->setPackage((new TestSyncMessagePackage)
                    ->setPackage(Packages::TEST_SYNC)
                    ->setMessageId(Str::uuid()->toString())
                    ->setCorrelationId(Str::uuid()->toString())
                    ->setSentTime(now())
                    ->setMessage(
                        (new TestMessage)
                            ->setString1('a1')
                            ->setString2('b2')
                    ))
        );
dd($result);
```

Example of submitting asynchronous command
```php
(new ServiceBus)->asyncPublish(
            (new AsyncPublishRequest)
                ->setPublisher(\App\Modules\ServiceBus\Enums\Publishers::TEST_LOCAL)
                ->setRoutingKey('*')
                ->setPackage(
                    (new TestMessagePackage())
                        ->setPackage(\App\Modules\ServiceBus\Enums\Packages::TEST_ASYNC_LOCAL)
                        ->setMessageId(Str::uuid()->toString())
                        ->setCorrelationId(Str::uuid()->toString())
                        ->setSentTime(now())
                        ->setMessage(
                            (new \App\Modules\ServiceBus\Packages\Messages\TestMessage())
                                ->setName('Sergey')
                        )
                )
        );
```
