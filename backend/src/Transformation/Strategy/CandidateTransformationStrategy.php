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
        $candidateDocument
            ->setEntityId($entity->getId())
            ->setFirstName($entity->getFirstName())
            ->setLastName($entity->getLastName())
            ->setPhone($entity->getPhone())
            ->setEmail($entity->getEmail())
            ->setAddress($entity->getAddress())
            ->setHireDate($entity->getHireDate());
        $resumeDocument = new ResumeDocument();
        $resumeDocument
            ->setCandidate($candidateDocument)
            ->setFilePath($entity->getResume()->getFilePath())
            ->setEntityId($entity->getResume()->getId());
        $candidateDocument->setResume($resumeDocument);
        return $candidateDocument;
    }
}