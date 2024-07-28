<?php

namespace App\Command\Candidate;

readonly class AddInterviewToCandidateCommand
{
    public function __construct(
        public int $candidateId,
        public int $interviewId
    ) {}
}