<?php

namespace App\Command\Candidate;

use App\Command\Abstract\AbstractCommand;
use App\Entity\Candidate;
use App\Message\Candidate\CandidateDeletedMessage;
use App\Services\Impl\CandidateService;

readonly class DeleteCandidateCommand extends AbstractCommand
{
    public function __construct(private Candidate $candidate, private CandidateService $candidateService)
    {
        parent::__construct($candidateService);
    }

    /**
     * @return int
     * @throws \Exception
     */
    public function execute(): int
    {
        if ($this->candidate->getId() == null) {
            throw new \Exception('Candidate ID is required');
        }
        $candidateIdBackup = $this->candidate->getId();
        $this->candidateService->deleteEntity($this->candidate->getId());
        return $candidateIdBackup;
    }
    public static function getMessageClass(): string
    {
        return CandidateDeletedMessage::Message::class;
    }
}