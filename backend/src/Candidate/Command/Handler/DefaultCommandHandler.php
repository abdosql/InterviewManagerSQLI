<?php

/**
 * @author Saqqal Abdelaziz <seqqal.abdelaziz@gmail.com>
 * @Linkedin https://www.linkedin.com/abdelaziz-saqqal
 */

namespace App\Candidate\Command\Handler;

use App\Candidate\Command\CommandInterface;
use Symfony\Component\Messenger\Exception\ExceptionInterface;
use Symfony\Component\Messenger\Exception\TransportException;
use Symfony\Component\Messenger\MessageBusInterface;

readonly class DefaultCommandHandler implements CommandHandlerInterface
{

    /**
     * @param CommandInterface $command
     */
    public function handle(CommandInterface $command): void
    {
        $command->execute();
    }
}