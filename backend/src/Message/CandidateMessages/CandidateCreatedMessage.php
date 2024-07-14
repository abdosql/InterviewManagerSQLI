<?php

namespace App\Message\CandidateMessages;
class CandidateCreatedMessage
{
    public function __construct(
        public int $id,
        public string $firstName,
        public string $lastName,
        public string $phone,
        public string $email,
        public string $address,
        public ?\DateTimeInterface $hireDate,
        public string $resumeFilePath
    ) {}
}
