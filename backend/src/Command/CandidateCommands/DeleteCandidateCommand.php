<?php

namespace App\Command\CandidateCommands;

use App\Command\CommandInterface;
use App\Entity\Candidate;
use App\Entity\Resume;
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
    public function execute(): Candidate
    {
        if ($this->candidate->getId() == null) {
            throw new \Exception('Candidate ID is required');
        }
        $deletedCandidate = $this->deepCloneCandidate($this->candidate);
        $this->candidateService->deleteEntity($this->candidate->getId());
        return $deletedCandidate;
    }
    public function deepCloneCandidate(Candidate $candidate): Candidate
    {
        //for now just resume men b3d other associations
        $clonedCandidate = clone $candidate;
        if ($candidate->getResume() instanceof Resume) {
            $clonedResume = clone $candidate->getResume();
            $clonedResume->setCandidate($clonedCandidate);
            $clonedCandidate->setResume($clonedResume);
        }
        return $clonedCandidate;
    }
}