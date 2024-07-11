<?php

namespace App\Command\CandidateCommands;

use DateTimeInterface;

readonly class CreateCandidateCommand
{
    public function __construct(
        public string             $firstName,
        public string             $lastName,
        public string             $phone,
        public string             $email,
        public string             $address,
        public ?DateTimeInterface $hireDate = null
    ) {}
}