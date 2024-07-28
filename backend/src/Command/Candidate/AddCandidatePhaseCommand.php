<?php

namespace App\Command\Candidate;

readonly class AddCandidatePhaseCommand
{
    public function __construct(
        public int $candidateId,
        public int $phaseId
    ) {}
}