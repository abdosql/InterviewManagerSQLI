<?php

namespace App\Command\CandidateCommands;

use DateTimeInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;

readonly class CreateCandidateCommand
{
    public function __construct(
        public string             $firstName,
        public string             $lastName,
        public string             $phone,
        public string             $email,
        public string             $address,
        public string $resumeFilePath,
        public ?DateTimeInterface $hireDate = null,
    ) {}
}