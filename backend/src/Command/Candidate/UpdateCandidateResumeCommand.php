<?php

namespace App\Command\Candidate;

readonly class UpdateCandidateResumeCommand
{
    public function __construct(
        public int   $candidateId,
        public array $resumeData
    ) {}
}