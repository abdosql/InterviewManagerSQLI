<?php
/**
 * @author Saqqal Abdelaziz <seqqal.abdelaziz@gmail.com>
 * @Linkedin https://www.linkedin.com/abdelaziz-saqqal
 */

namespace App\Interview\Command;

use App\Appreciation\Command\CommandInterface;
use App\Services\DatabasePersistence\EntityPersistenceServiceInterface;
use Symfony\Component\Messenger\MessageBusInterface;

readonly abstract class AbstractCommand implements CommandInterface
{
    public function __construct(
        private EntityPersistenceServiceInterface $service,
        private MessageBusInterface      $messageBus,
    )
    {

    }

    abstract public function execute(): mixed;
}