<?php

namespace App\Message\Abstract;

readonly class AbstractMessage
{
    public function __construct(
        protected int $id,
    ) {}

    public function getId(): int
    {
        return $this->id;
    }
}