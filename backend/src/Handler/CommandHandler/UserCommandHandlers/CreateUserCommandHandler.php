<?php

namespace App\Handler\CommandHandler\UserCommandHandlers;

use App\Command\UserCommands\CreateUserCommand;
use App\Handler\CommandHandler\CommandHandlerInterface;
use App\Message\UserMessages\UserCreatedMessage;
use Symfony\Component\Messenger\Exception\ExceptionInterface;
use Symfony\Component\Messenger\Exception\TransportException;
use Symfony\Component\Messenger\MessageBusInterface;

class CreateUserCommandHandler implements CommandHandlerInterface
{
    public function __construct(
        private MessageBusInterface $messageBus,
    ) {}

    /**
     * @throws ExceptionInterface
     */
    public function handle(object $command): void
    {
        if (!$command instanceof CreateUserCommand) {
            throw new \InvalidArgumentException('Invalid command type');
        }
        $user = $command->execute();
        $message = new UserCreatedMessage($user->getId());
        try {
            $this->messageBus->dispatch($message);
        }catch (TransportException $e) {
            throw new \RuntimeException('Failed to dispatch user created message');
        }
    }
}