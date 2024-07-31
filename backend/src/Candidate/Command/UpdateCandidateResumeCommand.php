<?php

namespace App\Candidate\Command;

readonly class UpdateCandidateResumeCommand
{
    public function __construct(
        public int   $candidateId,
        public array $resumeData
    ) {}
}