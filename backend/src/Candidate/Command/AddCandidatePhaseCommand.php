<?php

namespace App\Candidate\Command;

readonly class AddCandidatePhaseCommand
{
    public function __construct(
        public int $candidateId,
        public int $phaseId
    ) {}
}