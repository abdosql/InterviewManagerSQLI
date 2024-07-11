<?php

namespace App\Command\CandidateCommands;

readonly class AddInterviewToCandidateCommand
{
    public function __construct(
        public int $candidateId,
        public int $interviewId
    ) {}
}