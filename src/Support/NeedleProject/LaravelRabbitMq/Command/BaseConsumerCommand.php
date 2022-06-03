<?php

namespace SMSkin\ServiceBus\Support\NeedleProject\LaravelRabbitMq\Command;

use SMSkin\ServiceBus\Support\NeedleProject\LaravelRabbitMq\ConsumerInterface;
use Illuminate\Contracts\Container\BindingResolutionException;

/**
 * Class BaseConsumerCommand
 *
 * @package NeedleProject\LaravelRabbitMq\Command
 * @author  Adrian Tilita <adrian@tilita.ro>
 */
class BaseConsumerCommand extends \NeedleProject\LaravelRabbitMq\Command\BaseConsumerCommand
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'modules:esb:consume {consumer} {--time=60} {--messages=100} {--memory=64}';

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
