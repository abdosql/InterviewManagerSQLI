<?php

namespace App\Message\CandidateMessages;


readonly class CandidateCreatedMessage
{
    public function __construct(
        protected int $id,
    ) {}

    public function getId(): int
    {
        return $this->id;
    }
}