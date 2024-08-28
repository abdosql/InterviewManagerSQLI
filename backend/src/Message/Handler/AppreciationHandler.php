<?php
/**
 * @author Saqqal Abdelaziz <seqqal.abdelaziz@gmail.com>
 * @Linkedin https://www.linkedin.com/abdelaziz-saqqal
 */

namespace App\Message\Handler;

use App\Adapter\DataTransformationAdapter;
use App\Message\Appreciation\AppreciationAddMessage;
use App\Services\Impl\AppreciationService;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

readonly class AppreciationHandler
{
    public function __construct(
        private DataTransformationAdapter $transformationAdapter,
        private AppreciationService $appreciationService,
    ) {}

    /**
     * @param AppreciationAddMessage $message
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    #[AsMessageHandler]
    public function handleAddAppreciation(AppreciationAddMessage $message): void
    {
        $appreciationDocument = $this->transformationAdapter->transformToDocument($message->getId(), 'appreciation');
        $this->appreciationService->saveDocument($appreciationDocument);
    }
//
//    /**
//     * @throws ContainerExceptionInterface
//     * @throws MongoDBException
//     * @throws NotFoundExceptionInterface
//     */
//    #[AsMessageHandler]
//    public function handleUpdatedCandidate(CandidateUpdatedMessage $message): void
//    {
//        $updatedCandidateDocument = $this->transformationAdapter->transformToDocument($message->getId(), 'candidate');
//        $this->candidateService->updateDocument($message->getId(), $updatedCandidateDocument);
//    }
//    #[AsMessageHandler]
//    /**
//     * @throws MongoDBException
//     */
//    public function handleDeletedCandidate(CandidateDeletedMessage $message): void
//    {
//        $this->candidateService->deleteDocument($message->getId());
//    }
}