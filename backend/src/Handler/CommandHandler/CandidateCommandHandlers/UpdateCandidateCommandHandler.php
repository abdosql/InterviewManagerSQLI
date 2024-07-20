<?php

namespace App\Handler\CommandHandler\CandidateCommandHandlers;

use App\Command\CandidateCommands\UpdateCandidateCommand;
use App\Handler\CommandHandler\CommandHandlerInterface;
use App\Message\CandidateMessages\CandidateUpdatedMessage;
use Symfony\Component\Messenger\Exception\ExceptionInterface;
use Symfony\Component\Messenger\Exception\TransportException;
use Symfony\Component\Messenger\MessageBusInterface;

class UpdateCandidateCommandHandler implements CommandHandlerInterface
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
        if (!$command instanceof UpdateCandidateCommand){
            throw new \InvalidArgumentException('Invalid command');
        }
        $candidate = $command->execute();
        $message = new CandidateUpdatedMessage(
            $candidate->getId()
        );

        try {
            $this->messageBus->dispatch($message);
        }catch (TransportException $e) {
            throw new \RuntimeException($e->getMessage());
        }
    }
}