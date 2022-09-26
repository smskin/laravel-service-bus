<?php

namespace SMSkin\ServiceBus\Support\NeedleProject\LaravelRabbitMq\Entity;

use PhpAmqpLib\Exception\AMQPProtocolChannelException;
use SMSkin\ServiceBus\Support\NeedleProject\LaravelRabbitMq\PublisherInterface;
use SMSkin\ServiceBus\Support\NeedleProject\LaravelRabbitMq\ConsumerInterface;
use NeedleProject\LaravelRabbitMq\Entity\AMQPEntityInterface;
use Psr\Log\LoggerAwareInterface;
use Log;
use PhpAmqpLib\Wire\AMQPTable;

class QueueEntity extends \NeedleProject\LaravelRabbitMq\Entity\QueueEntity implements PublisherInterface, ConsumerInterface, AMQPEntityInterface, LoggerAwareInterface
{
    protected bool $working = true;

    /**
     * Create the Queue
     */
    public function create()
    {
        try {
//            $options = new AMQPTable([
//                'x-max-priority' => 3,
//            ]);

           // Log::alert('$this->attributes', ['data' => print_r($this->attributes, true)]);
            $this->getChannel()
                ->queue_declare(
                    $this->attributes['name'],
                    $this->attributes['passive'],
                    $this->attributes['durable'],
                    $this->attributes['exclusive'],
                    $this->attributes['auto_delete'],
                    $this->attributes['nowait'],
                    $this->attributes
                );
        } catch (AMQPProtocolChannelException $e) {
            // 406 is a soft error triggered for precondition failure (when redeclaring with different parameters)
            if (true === $this->attributes['throw_exception_on_redeclare'] || $e->amqp_reply_code !== 406) {
                throw $e;
            }
            // a failure trigger channels closing process
            $this->reconnect();
        }
    }

    public function bind()
    {
        if (!isset($this->attributes['bind']) || empty($this->attributes['bind'])) {
            return;
        }
        foreach ($this->attributes['bind'] as $bindItem) {
            try {
                $options = new AMQPTable([
                    'x-max-priority' => 3,
                ]);
                $this->getChannel()
                    ->queue_bind(
                        $this->attributes['name'],
                        $bindItem['exchange'],
                        $bindItem['routing_key'],
                        false,
                        $options,
                    );
            } catch (AMQPProtocolChannelException $e) {
                // 404 is the code for trying to bind to an non-existing entity
                if (true === $this->attributes['throw_exception_on_bind_fail'] || $e->amqp_reply_code !== 404) {
                    throw $e;
                }
                $this->reconnect();
            }
        }
    }

    protected function shouldStopConsuming(): bool
    {
        if (parent::shouldStopConsuming()) {
            return true;
        }

        return !$this->working;
    }

    public function stopConsuming()
    {
        parent::stopConsuming();
        $this->working = false;
    }
}
