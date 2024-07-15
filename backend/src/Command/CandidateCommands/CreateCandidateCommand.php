<?php

namespace App\Command\CandidateCommands;

use App\Entity\Candidate;
use Doctrine\ORM\EntityManagerInterface;

readonly class CreateCandidateCommand
{
    protected Candidate $candidate;
    private EntityManagerInterface $entityManager;
    public function __construct(EntityManagerInterface $entityManager, $candidate) {
        $this->candidate = $candidate;
        $this->entityManager = $entityManager;
    }

    public function __invoke(): Candidate
    {
        $this->entityManager->persist($this->candidate);
        $this->entityManager->flush();
        return $this->candidate;

    }
}