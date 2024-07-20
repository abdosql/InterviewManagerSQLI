<?php

namespace App\Message\CandidateMessages;

readonly class CandidateDeletedMessage
{
    protected int $id;
    /**
     * @param int|null $getId
     */
    public function __construct(?int $getId)
    {
        $this->id = $getId;
    }

    public function getId(): int
    {
        return $this->id;
    }
}