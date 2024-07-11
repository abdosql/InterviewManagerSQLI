<?php

namespace App\Command\CandidateCommands;

readonly class DeleteCandidateCommand
{
    public function __construct(
        public int $id
    ) {}
}