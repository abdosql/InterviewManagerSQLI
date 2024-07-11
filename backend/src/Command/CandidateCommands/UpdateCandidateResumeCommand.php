<?php

namespace App\Command\CandidateCommands;

readonly class UpdateCandidateResumeCommand
{
    public function __construct(
        public int   $candidateId,
        public array $resumeData
    ) {}
}