<?php

namespace App\MessageHandler\CandidateMessageHandlers;

use App\Message\CandidateMessages\CandidateCreatedMessage;
use App\Document\Candidate;
use Doctrine\ODM\MongoDB\DocumentManager;
use Doctrine\ODM\MongoDB\MongoDBException;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
class CandidateCreatedMessageHandler
{
    public function __construct(private DocumentManager $documentManager)
    {
    }

    /**
     * @throws MongoDBException
     * @return array<Candidate>
     */
    public function __invoke(CandidateCreatedMessage $message): array
    {
        $candidate = new Candidate();

        // Set Candidate properties
        $candidate->setAddress($message->address);

        // Persist and flush
        $this->documentManager->persist($candidate);
        $this->documentManager->flush();

        // Fetch all candidates
        $candidateRepository = $this->documentManager->getRepository(Candidate::class);
         dd($candidateRepository->findAll());
    }
}