<?php

use SMSkin\ServiceBus\Enums\Connections;
use SMSkin\ServiceBus\Enums\Consumers;
use SMSkin\ServiceBus\Enums\Exchanges;
use SMSkin\ServiceBus\Enums\Hosts;
use SMSkin\ServiceBus\Enums\Packages;
use SMSkin\ServiceBus\Enums\Publishers;
use SMSkin\ServiceBus\Enums\Queues;

return [
    'enums' => [
        'connections' => Connections::class,
        'exchanges' => Exchanges::class,
        'queues' => Queues::class,
        'consumers' => Consumers::class,
        'publishers' => Publishers::class,
        'packages' => Packages::class,
        'hosts' => Hosts::class
    ],
    'connections' => [
        'async' => [
            Connections::DEFAULT => [
                'hostname' => env('SERVICE_BUS_RABBITMQ_HOST', 'rabbitmq'),
                'port' => env('SERVICE_BUS_RABBITMQ_PORT', 5672),
                'username' => env('SERVICE_BUS_RABBITMQ_USER', 'admin'),
                'password' => env('SERVICE_BUS_RABBITMQ_PASSWORD', 'admin'),
                'vhost' => env('SERVICE_BUS_RABBITMQ_VHOST', '/'),
                'lazy' => true,
                'read_write_timeout' => 8,
                'connect_timeout' => 10,
                'heartbeat' => 4
            ]
        ],
        'sync' => [
            Hosts::LOCALHOST => [
                'api_token' => env('SERVICE_BUS_SYNC_HOST_API_TOKEN')
            ]
        ]
    ],
    'exchanges' => [
        Exchanges::DEFAULT
    ],
    'queues' => [
        Queues::DEFAULT
    ],
    'publishers' => [
        Publishers::TEST
    ],
    'consumers' => [
        Consumers::TEST
    ],
    'host' => [
        'active' => true,
        'api_token' => env('SERVICE_BUS_SYNC_HOST_API_TOKEN'),
        'route_prefix' => 'service-bus'
    ],
    'supervisor' => [
        'consumers' => [
            [
                'name' => Consumers::TEST,
                'time' => 60,
                'messages' => 100,
                'memory' => 64,
            ]
        ],
        'scale' => 1,
        'nice' => 0,
        'timeout' => 60
    ]
];