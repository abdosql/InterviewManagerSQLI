<?php

namespace App\Handler\CommandHandler\UserCommandHandlers;

use App\Command\CommandInterface;
use App\Command\UserCommands\CreateUserCommand;
use App\Command\UserCommands\DeleteUserCommand;
use App\Handler\CommandHandler\CommandHandlerInterface;
use App\Message\UserMessages\UserCreatedMessage;
use App\Message\UserMessages\UserDeletedMessage;
use Symfony\Component\Messenger\Exception\ExceptionInterface;
use Symfony\Component\Messenger\Exception\TransportException;
use Symfony\Component\Messenger\MessageBusInterface;

class DeleteUserCommandHandler implements CommandHandlerInterface
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
        if (!$command instanceof DeleteUserCommand) {
            throw new \InvalidArgumentException('Invalid command type');
        }
        $userId = $command->execute();
        $message = new UserDeletedMessage($userId);
        try {
            $this->messageBus->dispatch($message);
        }catch (TransportException $e) {
            throw new \RuntimeException('Failed to dispatch user created message'. $e->getMessage());
        }
    }
}