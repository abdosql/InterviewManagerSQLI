<?php

namespace App\Transformation\Strategy;

use App\Document\CandidateDocument;
use App\Document\ResumeDocument;
use App\Entity\Candidate;
use App\Transformation\TransformToDocumentStrategyInterface;
use Doctrine\ORM\EntityManagerInterface;

class CandidateTransformationStrategy implements TransformToDocumentStrategyInterface
{
    public function __construct(private EntityManagerInterface $entityManager)
    {
    }

    public function transformToDocument(int $entityId): CandidateDocument
    {
        $entity = $this->entityManager->getRepository(Candidate::class)->find($entityId);
        if (!$entity) {
            throw new \RuntimeException("Candidate not found with id: $entityId");
        }
        $candidateDocument = new CandidateDocument();
        $candidateDocument->setEntityId($entity->getId());
        $candidateDocument->setFirstName($entity->getFirstName());
        $candidateDocument->setLastName($entity->getLastName());
        $candidateDocument->setPhone($entity->getPhone());
        $candidateDocument->setEmail($entity->getEmail());
        $candidateDocument->setAddress($entity->getAddress());
        $candidateDocument->setHireDate($entity->getHireDate());
        $resumeDocument = new ResumeDocument();
        $resumeDocument->setCandidate($candidateDocument);
        $resumeDocument->setFilePath($entity->getResume()->getFilePath());
        $resumeDocument->setEntityId($entity->getResume()->getId());
        $candidateDocument->setResume($resumeDocument);
        return $candidateDocument;
    }
}