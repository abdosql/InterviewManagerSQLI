<?php

namespace App\Handler\CommandHandler\CandidateCommandHandlers;

use App\Command\CandidateCommands\UpdateCandidateCommand;
use App\Message\CandidateMessages\CandidateUpdatedMessage;
use Symfony\Component\Messenger\Exception\ExceptionInterface;
use Symfony\Component\Messenger\Exception\TransportException;
use Symfony\Component\Messenger\MessageBusInterface;

class UpdateCandidateCommandHandler
{
    public function __construct(
        private MessageBusInterface $messageBus,
    ) {}

    /**
     * @throws ExceptionInterface
     * @throws \Exception
     */
    public function handle(UpdateCandidateCommand $command): void
    {
        $candidate = ($command)();
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