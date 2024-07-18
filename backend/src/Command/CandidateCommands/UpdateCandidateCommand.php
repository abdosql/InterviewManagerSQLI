<?php

namespace App\Command\CandidateCommands;

use App\Entity\Candidate;
use App\Services\Impl\CandidateService;
use Doctrine\ORM\EntityManagerInterface;

readonly class UpdateCandidateCommand
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
    public function __invoke(): Candidate
    {
        if ($this->candidate->getId() == null) {
            throw new \Exception('Candidate ID is required');
        }
        $this->candidateService->updateEntity($this->candidate);
        return $this->candidate;
    }
}
