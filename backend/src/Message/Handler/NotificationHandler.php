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
use App\Message\Notification\NotificationCreatedMessage;
use App\Message\Notification\NotificationMarkedAsReadMessage;
use App\Services\Impl\CandidateService;
use App\Services\Impl\NotificationService;
use Doctrine\ODM\MongoDB\MongoDBException;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

class NotificationHandler
{
    public function __construct(
        private DataTransformationAdapter $transformationAdapter,
        private NotificationService $notificationService,
    ) {}

    /**
     * @param NotificationCreatedMessage $message
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    #[AsMessageHandler]
    public function handleSendNotification(NotificationCreatedMessage $message): void
    {
        $notificationDocument = $this->transformationAdapter->transformToDocument($message->getId(), 'notification');
        $this->notificationService->saveDocument($notificationDocument);
    }

    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     * @throws MongoDBException
     */
    #[AsMessageHandler]
    public function handleNotificationMarkedAsRead(NotificationMarkedAsReadMessage $message): void
    {
        $notificationDocument = $this->transformationAdapter->transformToDocument($message->getId(), 'notification');
        $this->notificationService->updateDocument($message->getId(), $notificationDocument);
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
//    #[AsMessageHandler]
//    /**
//     * @throws MongoDBException
//     */
//    public function handleDeletedCandidate(CandidateDeletedMessage $message): void
//    {
//        $this->candidateService->deleteDocument($message->getId());
//    }
}