<?php
/**
 * @author Saqqal Abdelaziz <seqqal.abdelaziz@gmail.com>
 * @Linkedin https://www.linkedin.com/abdelaziz-saqqal
 */

namespace App\Candidate\Query;

readonly class GetCandidateByEntityId implements QueryInterface
{
    private int $entityId;

    public function getEntityId(): int
    {
        return $this->entityId;
    }
}