<?php

namespace App\Command\CandidateCommands;

use App\Command\CommandInterface;
use App\Entity\Candidate;
use App\Services\Impl\CandidateService;

readonly class CreateCandidateCommand implements CommandInterface
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
        $this->candidateService->saveEntity($this->candidate);
        return $this->candidate->getId();
    }
}