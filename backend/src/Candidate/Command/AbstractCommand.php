<?php
/**
 * @author Saqqal Abdelaziz <seqqal.abdelaziz@gmail.com>
 * @Linkedin https://www.linkedin.com/abdelaziz-saqqal
 */

namespace App\Candidate\Command;

use App\Services\DatabasePersistence\EntityPersistenceServiceInterface;
use Symfony\Component\Messenger\MessageBusInterface;

readonly abstract class AbstractCommand implements CommandInterface
{
    public function __construct(
        private readonly EntityPersistenceServiceInterface $service,
        private readonly MessageBusInterface $messageBus,
    )
    {
        
    }
    abstract public function execute(): mixed;
}