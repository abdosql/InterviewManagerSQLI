<?php

namespace App\Candidate\Command;

readonly class AddInterviewToCandidateCommand
{
    public function __construct(
        public int $candidateId,
        public int $interviewId
    ) {}
}