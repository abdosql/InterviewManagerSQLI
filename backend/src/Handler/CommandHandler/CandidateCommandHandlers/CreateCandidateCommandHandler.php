<?php

namespace App\Handler\CommandHandler\CandidateCommandHandlers;

use App\Command\CandidateCommands\CreateCandidateCommand;
use App\Message\CandidateMessages\CandidateCreatedMessage;
use Symfony\Component\Messenger\Exception\ExceptionInterface;
use Symfony\Component\Messenger\Exception\TransportException;
use Symfony\Component\Messenger\MessageBusInterface;

readonly class CreateCandidateCommandHandler
{
    public function __construct(
        private MessageBusInterface $messageBus,
    ) {}

    /**
     * @throws ExceptionInterface
     */
    public function handle(CreateCandidateCommand $command): void
    {
        $candidate = ($command)();
        $message = new CandidateCreatedMessage(
            $candidate->getId()
        );

        try {
            $this->messageBus->dispatch($message);
        }catch (TransportException $e) {
            throw new \RuntimeException($e->getMessage());
        }
    }
}