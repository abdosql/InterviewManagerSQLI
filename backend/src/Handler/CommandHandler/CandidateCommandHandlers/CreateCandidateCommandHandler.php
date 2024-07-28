<?php

namespace App\Handler\CommandHandler\CandidateCommandHandlers;

use App\Command\Candidate\CreateCandidateCommand;
use App\Command\Candidate\UpdateCandidateCommand;
use App\Handler\CommandHandler\CommandHandlerInterface;
use App\Message\Candidate\CandidateCreatedMessage;
use Symfony\Component\Messenger\Exception\ExceptionInterface;
use Symfony\Component\Messenger\Exception\TransportException;
use Symfony\Component\Messenger\MessageBusInterface;

readonly class CreateCandidateCommandHandler implements CommandHandlerInterface
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
        if (!$command instanceof CreateCandidateCommand){
            throw new \InvalidArgumentException('Invalid command');
        }
        $candidateId = $command->execute();
        $message = new CandidateCreatedMessage(
            $candidateId
        );

        try {
            $this->messageBus->dispatch($message);
        }catch (TransportException $e) {
            throw new \RuntimeException('Failed to dispatch user created message');
        }
    }


}