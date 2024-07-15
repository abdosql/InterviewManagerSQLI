<?php

namespace App\Factory\DocumentFactory;

use App\Document\CandidateDocument;
use App\Document\ResumeDocument;
use App\Entity\Candidate;

class CandidateFactory
{
    public static function createDocumentFromEntity(Candidate $candidate): CandidateDocument
    {
        $candidateDocument = new CandidateDocument();
        $candidateDocument->setEntityId($candidate->getId());
        $candidateDocument->setFirstName($candidate->getFirstName());
        $candidateDocument->setLastName($candidate->getLastName());
        $candidateDocument->setPhone($candidate->getPhone());
        $candidateDocument->setEmail($candidate->getEmail());
        $candidateDocument->setAddress($candidate->getAddress());
        $candidateDocument->setHireDate($candidate->getHireDate());
        $resumeDocument = new ResumeDocument();
        $resumeDocument->setCandidate($candidateDocument);
        $resumeDocument->setFilePath($candidate->getResume()->getFilePath());
        $resumeDocument->setEntityId($candidate->getResume()->getId());
        $candidateDocument->setResume($resumeDocument);
        return $candidateDocument;
    }
}