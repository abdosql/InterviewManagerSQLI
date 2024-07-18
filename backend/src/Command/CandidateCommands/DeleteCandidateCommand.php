<?php

namespace App\Command\CandidateCommands;

use App\Entity\Candidate;
use App\Services\Impl\CandidateService;

readonly class DeleteCandidateCommand
{
    protected Candidate $candidate;
    private CandidateService $candidateService;
    public function __construct($candidate, CandidateService $candidateService) {
        $this->candidate = $candidate;
        $this->candidateService = $candidateService;
    }

    public function __invoke(): Candidate
    {
        $this->candidateService->deleteEntity($this->candidate->getId());
        return $this->candidate;
    }
}