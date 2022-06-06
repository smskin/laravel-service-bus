<?php

namespace SMSkin\ServiceBus\Support\NeedleProject\LaravelRabbitMq\Command;

use Exception;
use SMSkin\ServiceBus\Support\NeedleProject\LaravelRabbitMq\Container;
use NeedleProject\LaravelRabbitMq\Entity\QueueEntity;
use NeedleProject\LaravelRabbitMq\PublisherInterface;
use SMSkin\LaravelSupport\BaseCommand;

/**
 * Class DeleteAllCommand
 *
 * @package NeedleProject\LaravelRabbitMq\Commad
 * @author  Adrian Tilita <adrian@tilita.ro>
 */
class DeleteAllCommand extends BaseCommand
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'service-bus:delete-all';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Delete all queues, exchanges and binds that are defined in entities AND referenced to' .
    ' either a publisher or a consumer';

    /**
     * @var Container
     */
    private Container $container;

    /**
     * CreateEntitiesCommand constructor.
     *
     * @param Container $container
     */
    public function __construct(Container $container)
    {
        $this->container = $container;
        parent::__construct();
    }

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $hasErrors = false;
        /** @var PublisherInterface $entity */
        foreach ($this->container->getPublishers() as $publisherName => $entity) {
            try {
                $entity->delete();
                $this->output->writeln(
                    sprintf(
                        "Deleted entity <info>%s</info> for publisher [<fg=yellow>%s</>]",
                        $entity->getAliasName(),
                        $publisherName
                    )
                );
            } catch (Exception $e) {
                $hasErrors = true;
                $this->output->error(
                    sprintf(
                        "Could not delete entity %s for publisher [%s], got:\n%s",
                        $entity->getAliasName(),
                        $publisherName,
                        $e->getMessage()
                    )
                );
            }
        }

        foreach ($this->container->getConsumers() as $consumerAliasName => $entity) {
            try {
                /** @var QueueEntity $entity */
                $entity->delete();
                $this->output->writeln(
                    sprintf(
                        "Deleted entity <info>%s</info> for consumer [<fg=yellow>%s</>]",
                        $entity->getAliasName(),
                        $consumerAliasName
                    )
                );
            } catch (Exception $e) {
                $hasErrors = true;
                $this->output->error(
                    sprintf(
                        "Could not delete entity %s for consumer [%s], got:\n%s",
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
