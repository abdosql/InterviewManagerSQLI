<?php
/**
 * @author Saqqal Abdelaziz <seqqal.abdelaziz@gmail.com>
 * @Linkedin https://www.linkedin.com/abdelaziz-saqqal
 */

namespace App\Candidate\Command\Abstract;

use App\Candidate\Command\CommandInterface;
use App\Services\DatabasePersistence\EntityPersistenceServiceInterface;

readonly abstract class AbstractCommand implements CommandInterface
{
    public function __construct(private EntityPersistenceServiceInterface $service)
    {
    }

    abstract public function execute(): mixed;
    abstract public static function getMessageClass(): string;

}