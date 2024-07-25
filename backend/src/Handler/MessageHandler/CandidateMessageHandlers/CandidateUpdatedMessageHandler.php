<?php

namespace App\Handler\MessageHandler\CandidateMessageHandlers;

use App\Adapter\DataTransformationAdapter;
use App\Entity\Candidate;
use App\Message\CandidateMessages\CandidateUpdatedMessage;
use App\Services\Factory\ServiceFactory;
use App\Services\Impl\CandidateService;
use Doctrine\ODM\MongoDB\DocumentManager;
use Doctrine\ODM\MongoDB\MongoDBException;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
class CandidateUpdatedMessageHandler
{
    public function __construct(
        private DataTransformationAdapter $transformationAdapter,
        private CandidateService $candidateService
    )
    {}

    /**
     * @param CandidateUpdatedMessage $message
     * @throws MongoDBException
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function __invoke(CandidateUpdatedMessage $message): void
    {
        $candidateEntity = $this->getCandidateEntityFromMessage($message);
        $updatedCandidateDocument = $this->transformationAdapter->transformToDocument($candidateEntity, 'candidate');
        $this->candidateService->updateDocument($message->getId(), $updatedCandidateDocument);
    }

    public function getCandidateEntityFromMessage(CandidateUpdatedMessage $message): Candidate
    {
        return $this->candidateService->findEntity($message->getId());
    }
}