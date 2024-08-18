<?php

namespace App\Transformation\Strategy;

use App\Document\CandidateDocument;
use App\Document\ResumeDocument;
use App\Entity\Candidate;
use App\Entity\Resume;
use App\Transformation\TransformToDocumentStrategyInterface;
use App\Transformation\TransformToEntityStrategyInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag;

#[AutoconfigureTag("app.transform_to_entity_strategy", ['type' => 'candidate'])]
#[AutoconfigureTag("app.transform_to_document_strategy", ['type' => 'candidate'])]
readonly class CandidateTransformationStrategy implements TransformToDocumentStrategyInterface, TransformToEntityStrategyInterface
{
    public function __construct(private EntityManagerInterface $entityManager)
    {
    }

    public function transformToDocument(int $entityId): CandidateDocument
    {
        $entity = $this->entityManager->getRepository(Candidate::class)->find($entityId);
        if (null === $entity) {
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

    public function transformToEntity(object $document): Candidate
    {
        $candidate = new Candidate();
        $candidate
            ->setFirstName($document->getFirstName())
            ->setLastName($document->getLastName())
            ->setPhone($document->getPhone())
            ->setEmail($document->getEmail())
            ->setAddress($document->getAddress())
            ->setHireDate($document->getHireDate());
        $resume = new Resume();
        $resume
            ->setCandidate($candidate)
            ->setFilePath($document->getResume()->getFilePath());
        $candidate->setResume($resume);

        return $candidate;
    }
}