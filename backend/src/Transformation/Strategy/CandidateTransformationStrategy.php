<?php

namespace App\Transformation\Strategy;

use App\Document\CandidateDocument;
use App\Document\ResumeDocument;
use App\Entity\Candidate;
use App\Transformation\TransformToDocumentStrategyInterface;

class CandidateTransformationStrategy implements TransformToDocumentStrategyInterface
{

    public function transformToDocument($entity): CandidateDocument
    {
        if (!$entity instanceof Candidate) {
            throw new \InvalidArgumentException('Expected instance of Candidate');
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