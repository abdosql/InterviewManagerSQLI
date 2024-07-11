<?php

namespace App\Command\CandidateCommands;

use DateTimeInterface;

readonly class UpdateCandidateCommand
{
    public function __construct(
        public int                $id,
        public ?string            $firstName = null,
        public ?string            $lastName = null,
        public ?string            $phone = null,
        public ?string            $email = null,
        public ?string            $address = null,
        public ?DateTimeInterface $hireDate = null
    ) {}
}
