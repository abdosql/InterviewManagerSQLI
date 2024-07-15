<?php

namespace App\MessageHandler\CandidateMessageHandlers;

use App\Adapter\DataTransformationAdapter;
use App\Entity\Candidate;
use App\Factory\DocumentFactory\CandidateFactory;
use App\Message\CandidateMessages\CandidateCreatedMessage;
use App\Document\CandidateDocument;
use App\Document\ResumeDocument;
use Doctrine\ODM\MongoDB\DocumentManager;
use Doctrine\ODM\MongoDB\MongoDBException;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
class CandidateCreatedMessageHandler
{
    public function __construct(
        private DocumentManager $documentManager,
        private EntityManagerInterface $entityManager,
        private DataTransformationAdapter $transformationAdapter
    )
    {
    }

    /**
     *@throws MongoDBException
     */
    public function __invoke(CandidateCreatedMessage $message): void
    {
        $candidateEntity = $this->getCandidateEntityFromMessage($message);

        $candidateDocument = $this->transformationAdapter->transformToDocument($candidateEntity, 'candidate');
        $this->documentManager->persist($candidateDocument);
        $this->documentManager->flush();


        $queryBuilder = $this->documentManager->createQueryBuilder(CandidateDocument::class);
        $query = $queryBuilder->getQuery();
        $candidates = $query->execute()->toArray(); // Fetch all candidates as an array
        dd($candidates); // Use dump() instead of dd() to continue execution after dumping
    }

    public function getCandidateEntityFromMessage(CandidateCreatedMessage $message): Candidate
    {
        return $this->entityManager->getRepository(Candidate::class)->find($message->getId());

    }
}