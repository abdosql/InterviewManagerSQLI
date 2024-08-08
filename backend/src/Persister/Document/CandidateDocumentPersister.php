<?php

namespace App\Persister\Document;

use App\Document\CandidateDocument;

class CandidateDocumentPersister extends AbstractDocumentPersister
{
    public function delete(object $document, bool $flush = true): void
    {
        if (!$document instanceof CandidateDocument) {
            throw new \InvalidArgumentException("Document must be an instance of CandidateDocument");
        }
        $this->documentManager->remove($document->getResume());
        foreach ($document->getInterviews() as $interview) {
            $this->documentManager->remove($interview);
        }

        foreach ($document->getCandidatePhases() as $candidatePhase) {
            $this->documentManager->remove($candidatePhase);
        }
        parent::delete($document, $flush);
    }
}