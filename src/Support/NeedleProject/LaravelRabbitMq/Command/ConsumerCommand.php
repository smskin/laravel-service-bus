<?php

namespace SMSkin\ServiceBus\Support\NeedleProject\LaravelRabbitMq\Command;

use NeedleProject\LaravelRabbitMq\Command\BaseConsumerCommand;
use SMSkin\ServiceBus\Support\NeedleProject\LaravelRabbitMq\ConsumerInterface;
use Illuminate\Contracts\Container\BindingResolutionException;

/**
 * Class BaseConsumerCommand
 *
 * @package NeedleProject\LaravelRabbitMq\Command
 * @author  Adrian Tilita <adrian@tilita.ro>
 */
class ConsumerCommand extends BaseConsumerCommand
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'service-bus:consume {consumer} {--time=60} {--messages=100} {--memory=64}';

    /**
     * @param string $consumerAliasName
     * @return ConsumerInterface
     * @throws BindingResolutionException
     */
    protected function getConsumer(string $consumerAliasName): ConsumerInterface
    {
        return app()->make(ConsumerInterface::class, [$consumerAliasName]);
    }
}
