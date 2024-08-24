<?php

namespace App\Transformation\Strategy;

use App\Document\NotificationDocument;
use App\Entity\Notification;
use App\Transformation\TransformToDocumentStrategyInterface;
use App\Transformation\TransformToEntityStrategyInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag;

#[AutoconfigureTag("app.transform_to_entity_strategy", ['type' => 'notification'])]
#[AutoconfigureTag("app.transform_to_document_strategy", ['type' => 'notification'])]
readonly class NotificationTransformationStrategy implements TransformToDocumentStrategyInterface, TransformToEntityStrategyInterface
{
    public function __construct(private EntityManagerInterface $entityManager)
    {
    }

    public function transformToDocument(int $entityId): NotificationDocument
    {
        $entity = $this->entityManager->getRepository(Notification::class)->find($entityId);
        if ($entity instanceof Notification) {
            throw new \RuntimeException("Notification not found with id: $entityId");
        }
        $notificationDocument = new NotificationDocument();
        $notificationDocument
            ->setEntityId($entityId)
            ->setUser($entity->getUser())
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