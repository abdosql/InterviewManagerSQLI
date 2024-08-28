<?php
/**
 * @author Saqqal Abdelaziz <seqqal.abdelaziz@gmail.com>
 * @Linkedin https://www.linkedin.com/abdelaziz-saqqal
 */

namespace App\User\Command\Handler;

use App\Appreciation\Command\CommandInterface;
use Symfony\Component\Messenger\Exception\ExceptionInterface;
use Symfony\Component\Messenger\Exception\TransportException;
use Symfony\Component\Messenger\MessageBusInterface;

readonly class DefaultCommandHandler implements CommandHandlerInterface
{
    /**
     * @param object $command
     * @return void
     */
    public function handle(object $command): void
    {
        $command->execute();
    }
}