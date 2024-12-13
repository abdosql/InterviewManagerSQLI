<?php
/**
 * @author Saqqal Abdelaziz <seqqal.abdelaziz@gmail.com>
 * @Linkedin https://www.linkedin.com/abdelaziz-saqqal
 */

namespace App\Message\Handler;

use App\Adapter\DataTransformationAdapter;
use App\Message\Candidate\CandidateCreatedMessage;
use App\Message\Candidate\CandidateDeletedMessage;
use App\Message\Candidate\CandidateUpdatedMessage;
use App\Services\Impl\CandidateService;
use Doctrine\ODM\MongoDB\MongoDBException;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

readonly class CandidateHandler
{
    public function __construct(
        private DataTransformationAdapter $transformationAdapter,
        private CandidateService $candidateService,
    ) {}

    /**
     * @param CandidateCreatedMessage $message
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    #[AsMessageHandler]
    public function handleNewCandidate(CandidateCreatedMessage $message): void
    {
        $candidateDocument = $this->transformationAdapter->transformToDocument($message->getId(), 'candidate');
        $this->candidateService->saveDocument($candidateDocument);
    }

    /**
     * @throws ContainerExceptionInterface
     * @throws MongoDBException
     * @throws NotFoundExceptionInterface
     */
    #[AsMessageHandler]
    public function handleUpdatedCandidate(CandidateUpdatedMessage $message): void
    {
        $updatedCandidateDocument = $this->transformationAdapter->transformToDocument($message->getId(), 'candidate');
        $this->candidateService->updateDocument($message->getId(), $updatedCandidateDocument);
    }
    #[AsMessageHandler]
    /**
     * @throws MongoDBException
     */
    public function handleDeletedCandidate(CandidateDeletedMessage $message): void
    {
        $this->candidateService->deleteDocument($message->getId());
    }
}