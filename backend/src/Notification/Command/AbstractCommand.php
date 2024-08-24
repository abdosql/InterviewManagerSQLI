<?php
/**
 * @author Saqqal Abdelaziz <seqqal.abdelaziz@gmail.com>
 * @Linkedin https://www.linkedin.com/abdelaziz-saqqal
 */

namespace App\Notification\Command;

use App\Candidate\Command\CommandInterface;
use App\Services\DatabasePersistence\EntityPersistenceServiceInterface;
use Symfony\Component\Messenger\MessageBusInterface;

readonly abstract class AbstractCommand implements CommandInterface
{
    public function __construct(
        protected readonly EntityPersistenceServiceInterface $service,
        protected readonly MessageBusInterface $messageBus,
    )
    {

    }

    abstract public function execute(): mixed;
}