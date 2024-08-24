<?php
/**
 * @author Saqqal Abdelaziz <seqqal.abdelaziz@gmail.com>
 * @Linkedin https://www.linkedin.com/abdelaziz-saqqal
 */

namespace App\User\Command;

use App\Candidate\Command\CommandInterface;
use App\Services\DatabasePersistence\EntityPersistenceServiceInterface;

readonly abstract class AbstractCommand implements CommandInterface
{
    public function __construct(protected EntityPersistenceServiceInterface $service)
    {
    }

    abstract public function execute(): mixed;
}