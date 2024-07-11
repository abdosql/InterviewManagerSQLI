<?php

namespace App\Command\CandidateCommands;

readonly class AddCandidatePhaseCommand
{
    public function __construct(
        public int $candidateId,
        public int $phaseId
    ) {}
}