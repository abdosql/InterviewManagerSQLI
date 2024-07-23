<?php

namespace App\Message\UserMessages;

class UserCreatedMessage
{
    public function __construct(
        protected int $id,
    ) {}

    public function getId(): int
    {
        return $this->id;
    }
}