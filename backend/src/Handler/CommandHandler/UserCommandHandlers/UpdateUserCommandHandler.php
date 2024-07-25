<?php

namespace App\Handler\CommandHandler\UserCommandHandlers;

use App\Command\UserCommands\UpdateUserCommand;
use App\Handler\CommandHandler\CommandHandlerInterface;
use App\Message\UserMessages\UserUpdatedMessage;
use Symfony\Component\Messenger\Exception\ExceptionInterface;
use Symfony\Component\Messenger\Exception\TransportException;
use Symfony\Component\Messenger\MessageBusInterface;

class UpdateUserCommandHandler implements CommandHandlerInterface
{
    public function __construct(
        private MessageBusInterface $messageBus,
    ) {}


    /**
     * @throws ExceptionInterface
     * @throws \Exception
     */
    public function handle(object $command): void
    {
        if (!$command instanceof UpdateUserCommand){
            throw new \InvalidArgumentException('Invalid command');
        }
        $userId = $command->execute();
        $message = new UserUpdatedMessage(
            $userId
        );

        try {
            $this->messageBus->dispatch($message);
        }catch (TransportException $e) {
            throw new \RuntimeException($e->getMessage());
        }
    }
}