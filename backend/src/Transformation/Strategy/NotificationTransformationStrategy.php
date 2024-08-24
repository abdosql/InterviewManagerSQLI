<?php

namespace App\Transformation\Strategy;

use App\Document\NotificationDocument;
use App\Entity\Notification;
use App\Services\Impl\NotificationService;
use App\Services\Impl\UserService;
use App\Transformation\TransformToDocumentStrategyInterface;
use App\Transformation\TransformToEntityStrategyInterface;
use Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag;

#[AutoconfigureTag("app.transform_to_entity_strategy", ['type' => 'notification'])]
#[AutoconfigureTag("app.transform_to_document_strategy", ['type' => 'notification'])]
readonly class NotificationTransformationStrategy implements TransformToDocumentStrategyInterface, TransformToEntityStrategyInterface
{
    public function __construct(
        private  NotificationService $notificationService,
        private  UserService $userService,
    )
    {
    }

    /**
     * @param int $entityId
     * @return NotificationDocument
     */
    public function transformToDocument(int $entityId): NotificationDocument
    {
        $entity = $this->notificationService->findEntity($entityId);
        $userDocument = $this->userService->findDocument($entity->getUser()->getId());

        $notificationDocument = new NotificationDocument();
        $notificationDocument
            ->setEntityId($entityId)
            ->setUser($userDocument)
            ->setContent($entity->getContent())
            ->setNotificationDate($entity->getNotificationDate())
        ;
        return $notificationDocument;
    }

    /**
     * @param object $document
     * @return Notification
     */
    public function transformToEntity(object $document): Notification
    {
        if (!$document instanceof NotificationDocument){
            throw new \InvalidArgumentException("Document must be an instance of NotificationDocument");
        }

        return $this->entityManager->getRepository(Notification::class)->find($document->getEntityId());
    }
}