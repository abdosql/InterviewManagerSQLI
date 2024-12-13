<?php
/**
 * @author Saqqal Abdelaziz <seqqal.abdelaziz@gmail.com>
 * @Linkedin https://www.linkedin.com/abdelaziz-saqqal
 */

namespace App\Message\Handler;

use App\Adapter\DataTransformationAdapter;
use App\Message\Interview\InterviewCreatedMessage;
use App\Message\Interview\InterviewDeletedMessage;
use App\Message\Interview\InterviewStatusAddMessage;
use App\Services\Impl\InterviewService;
use App\Services\Impl\InterviewStatusService;
use Doctrine\ODM\MongoDB\MongoDBException;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

readonly class InterviewHandler
{
    public function __construct(
        private DataTransformationAdapter $transformationAdapter,
        private InterviewService $interviewService,
        private InterviewStatusService $interviewStatusService,
    ) {}

    /**
     * @param InterviewCreatedMessage $message
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    #[AsMessageHandler]
    public function handleNewInterview(InterviewCreatedMessage $message): void
    {
        $interviewDocument = $this->transformationAdapter->transformToDocument($message->getId(), 'interview');
        $this->interviewService->saveDocument($interviewDocument);
    }

    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    #[AsMessageHandler]
    public function handleAddInterviewStatus(InterviewStatusAddMessage $message): void
    {
        $interviewDocument = $this->transformationAdapter->transformToDocument($message->getId(), 'interviewStatus');
        $this->interviewStatusService->saveDocument($interviewDocument);
    }

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
    #[AsMessageHandler]
    /**
     * @throws MongoDBException
     */
    public function handleDeletedInterview(InterviewDeletedMessage $message): void
    {
        $this->interviewService->deleteDocument($message->getId());
    }
}