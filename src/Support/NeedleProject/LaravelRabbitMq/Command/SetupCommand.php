<?php

namespace SMSkin\ServiceBus\Support\NeedleProject\LaravelRabbitMq\Command;

use Exception;
use SMSkin\ServiceBus\Support\NeedleProject\LaravelRabbitMq\Container;
use SMSkin\ServiceBus\Support\NeedleProject\LaravelRabbitMq\PublisherInterface;
use NeedleProject\LaravelRabbitMq\ConsumerInterface;
use NeedleProject\LaravelRabbitMq\Entity\AMQPEntityInterface;
use NeedleProject\LaravelRabbitMq\Entity\ExchangeEntity;
use NeedleProject\LaravelRabbitMq\Entity\QueueEntity;
use PhpAmqpLib\Exception\AMQPProtocolChannelException;
use SMSkin\LaravelSupport\BaseCommand;

/**
 * Class SetupCommand
 *
 * @package NeedleProject\LaravelRabbitMq\Commad
 * @author  Adrian Tilita <adrian@tilita.ro>
 */
class SetupCommand extends BaseCommand
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'service-bus:setup {--force}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create all queues, exchanges and binds that are defined in entities AND referenced to' .
    ' either a publisher or a consumer';

    private Container $container;

    /**
     * CreateEntitiesCommand constructor.
     */
    public function __construct(Container $container)
    {
        $this->container = $container;
        parent::__construct();
    }

    /**
     * @throws AMQPProtocolChannelException
     * @noinspection PhpDocRedundantThrowsInspection
     */
    private function createEntity(
        AMQPEntityInterface $entity,
        string $type,
        string $resourceName,
        bool $forceRecreate = false
    ) {
        if ($forceRecreate === true) {
            $this->output->writeln(
                sprintf(
                    "Deleting <info>%s</info> <fg=yellow>%s</>",
                    (string)($entity instanceof QueueEntity) ? 'QUEUE' : 'EXCHANGE',
                    $entity->getAliasName()
                )
            );
            $entity->delete();
        }

        $entity->create();
        $this->output->writeln(
            sprintf(
                "Created <info>%s</info> <fg=yellow>%s</> for %s [<fg=yellow>%s</>]",
                (string)($entity instanceof QueueEntity) ? 'QUEUE' : 'EXCHANGE',
                $entity->getAliasName(),
                $type,
                $resourceName
            )
        );
    }

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $forceRecreate = $this->input->getOption('force');

        $hasErrors = false;
        /** @var QueueEntity|ExchangeEntity $entity */
        foreach ($this->container->getPublishers() as $publisherName => $entity) {
            try {
                $this->createEntity($entity, 'publisher', $publisherName, $forceRecreate);
            } catch (AMQPProtocolChannelException $e) {
                $hasErrors = true;
                $this->output->error(
                    sprintf(
                        "Could not create entity %s for publisher [%s], got:\n%s",
                        $entity->getAliasName(),
                        $publisherName,
                        $e->getMessage()
                    )
                );
                $entity->reconnect();
            }
        }

        /** @var QueueEntity|ExchangeEntity $entity */
        foreach ($this->container->getConsumers() as $publisherName => $entity) {
            try {
                $this->createEntity($entity, 'consumer', $publisherName, $forceRecreate);
            } catch (AMQPProtocolChannelException $e) {
                $hasErrors = true;
                $this->output->error(
                    sprintf(
                        "Could not create entity %s for consumer [%s], got:\n%s",
                        $entity->getAliasName(),
                        $publisherName,
                        $e->getMessage()
                    )
                );
                $entity->reconnect();
            }
        }

        $this->output->block("Create binds");
        /** @var PublisherInterface $entity */
        foreach ($this->container->getPublishers() as $publisherName => $entity) {
            try {
                $entity->bind();
                $this->output->writeln(
                    sprintf(
                        "Created bind <info>%s</info> for publisher [<fg=yellow>%s</>]",
                        $entity->getAliasName(),
                        $publisherName
                    )
                );
            } catch (Exception $e) {
                $hasErrors = true;
                $this->output->error(
                    sprintf(
                        "Could not bind entity %s for publisher [%s], got:\n%s",
                        $entity->getAliasName(),
                        $publisherName,
                        $e->getMessage()
                    )
                );
            }
        }

        /** @var ConsumerInterface $entity */
        foreach ($this->container->getConsumers() as $consumerAliasName => $entity) {
            try {
                $entity->bind();
                $this->output->writeln(
                    sprintf(
                        "Bind entity <info>%s</info> for consumer [<fg=yellow>%s</>]",
                        $entity->getAliasName(),
                        $consumerAliasName
                    )
                );
            } catch (Exception $e) {
                $hasErrors = true;
                $this->output->error(
                    sprintf(
                        "Could not create bind %s for consumer [%s], got:\n%s",
                        $entity->getAliasName(),
                        $consumerAliasName,
                        $e->getMessage()
                    )
                );
            }
        }
        return (int)$hasErrors;
    }
}
