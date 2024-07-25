<?php

namespace App\Command\CandidateCommands;

use App\Command\CommandInterface;
use App\Entity\Candidate;
use App\Services\Impl\CandidateService;
use Doctrine\ORM\EntityManagerInterface;

readonly class UpdateCandidateCommand implements CommandInterface
{
    protected Candidate $candidate;
    private CandidateService $candidateService;
    public function __construct(Candidate $candidate, CandidateService $candidateService) {
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
        $this->candidateService->updateEntity($this->candidate);
        return $this->candidate->getId();
    }
}
