<?php

namespace App\Command\CandidateCommands;

use App\Command\CommandInterface;
use App\Entity\Candidate;
use App\Services\Impl\CandidateService;

readonly class DeleteCandidateCommand implements CommandInterface
{
    protected Candidate $candidate;
    private CandidateService $candidateService;
    public function __construct($candidate, CandidateService $candidateService) {
        $this->candidate = $candidate;
        $this->candidateService = $candidateService;
    }

    /**
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
}