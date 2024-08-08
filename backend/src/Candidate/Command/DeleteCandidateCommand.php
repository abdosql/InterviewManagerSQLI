<?php

namespace App\Candidate\Command;

use App\Entity\Candidate;
use App\Message\Candidate\CandidateDeletedMessage;
use App\Services\Impl\CandidateService;
use Symfony\Component\Messenger\Exception\ExceptionInterface;
use Symfony\Component\Messenger\Exception\TransportException;
use Symfony\Component\Messenger\MessageBusInterface;

readonly class DeleteCandidateCommand extends AbstractCommand
{
    public function __construct(private Candidate $candidate,
                                private CandidateService $candidateService,
                                private MessageBusInterface $messageBus,
    )
    {
        parent::__construct($candidateService, $messageBus);
    }

    /**
     * @return int
     * @throws \Exception
     * @throws ExceptionInterface
     */
    public function execute(): int
    {
        $candidateIdBackup = $this->candidate->getId();
        $this->candidateService->deleteEntity($this->candidate->getId());
        $message = new CandidateDeletedMessage($candidateIdBackup);
        try {
            $this->messageBus->dispatch($message);
        }catch (TransportException $e) {
            throw new \RuntimeException('Failed to dispatch '.$message::class." : ". $e->getMessage());
        }
        return $candidateIdBackup;
    }
}