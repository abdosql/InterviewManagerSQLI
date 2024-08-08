<?php

namespace App\Candidate\Command;

use App\Entity\Candidate;
use App\Message\Candidate\CandidateCreatedMessage;
use App\Services\Impl\CandidateService;
use Symfony\Component\Messenger\Exception\ExceptionInterface;
use Symfony\Component\Messenger\Exception\TransportException;
use Symfony\Component\Messenger\MessageBusInterface;

readonly class CreateCandidateCommand extends AbstractCommand
{
    public function __construct(private Candidate $candidate,
                                private CandidateService $candidateService,
                                private MessageBusInterface $messageBus,
    )
    {
        parent::__construct($candidateService, $messageBus);
    }

    /**
     * @return mixed
     * @throws ExceptionInterface
     */
    public function execute(): int
    {
        $this->candidateService->saveEntity($this->candidate);
        $message = new CandidateCreatedMessage($this->candidate->getId());
        try {
            $this->messageBus->dispatch($message);
        }catch (TransportException $e) {
            throw new \RuntimeException('Failed to dispatch '.$message::class." : ". $e->getMessage());
        }
        return $this->candidate->getId();
    }
}