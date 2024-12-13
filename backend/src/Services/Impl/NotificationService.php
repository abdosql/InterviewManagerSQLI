<?php
/**
 * @author Saqqal Abdelaziz <seqqal.abdelaziz@gmail.com>
 * @Linkedin https://www.linkedin.com/abdelaziz-saqqal
 */

namespace App\Services\Impl;

use App\Document\NotificationDocument;
use App\Entity\Notification;
use App\Persister\Document\NotificationDocumentPersister;
use App\Persister\DocumentPersisterInterface;
use App\Persister\Entity\NotificationEntityPersister;
use App\Persister\EntityPersisterInterface;
use App\Services\DatabasePersistence\DocumentPersistenceServiceInterface;
use App\Services\DatabasePersistence\EntityPersistenceServiceInterface;
use Doctrine\ODM\MongoDB\DocumentManager;
use Doctrine\ODM\MongoDB\DocumentNotFoundException;
use Doctrine\ODM\MongoDB\MongoDBException;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\DependencyInjection\Attribute\Autowire;

readonly class NotificationService implements DocumentPersistenceServiceInterface, EntityPersistenceServiceInterface
{
    public function __construct(
        #[Autowire(service: NotificationDocumentPersister::class)]
        private DocumentPersisterInterface $documentPersister,
        #[Autowire(service: NotificationEntityPersister::class)]
        private EntityPersisterInterface   $entityPersister,
        private DocumentManager            $documentManager,
        private EntityManagerInterface     $entityManager
    ){}
    /**
     * @param object $document
     */
    public function saveDocument(object $document): void
    {
        if (!$document instanceof NotificationDocument) {
            throw new \InvalidArgumentException("Document must be an instance of NotificationDocument");
        }
        $this->documentPersister->save($document);
    }

    /**
     * @throws MongoDBException
     */
    public function updateDocument(int $id, object $document): void
    {
        if (!$document instanceof NotificationDocument) {
            throw new \InvalidArgumentException("Document must be an instance of NotificationDocument");
        }
        $NotificationDocument = $this->findDocumentByEntity($id);
        if (!$NotificationDocument){
            throw new MongoDBException("Notification not found");
        }
        $NotificationDocument->setDocument($document);
        $this->documentPersister->update($NotificationDocument);
    }

    /**
     * @throws MongoDBException
     */
    public function deleteDocument(int $entityId): void
    {
        $NotificationDocument = $this->findDocumentByEntity($entityId);
        if (!$NotificationDocument) {
            throw new DocumentNotFoundException("Notification not found");
        }
        $this->documentPersister->delete($NotificationDocument);
    }

    public function findDocument($id)
    {
        // TODO: Implement findDocument() method.
    }

    public function findAllDocuments(): array
    {
        // TODO: Implement findAllDocuments() method.
    }

    public function saveEntity(object $entity): void
    {
        if (!$entity instanceof Notification) {
            throw new \InvalidArgumentException("Document must be an instance of NotificationDocument");
        }
        $this->entityPersister->save($entity);
    }

    public function updateEntity(object $entity): void
    {
        if (!$entity instanceof Notification){
            throw new \InvalidArgumentException("Entity must be an instance of Notification");
        }
        $this->entityPersister->update($entity);
    }

    public function deleteEntity(int $entityId): void
    {
        $this->entityPersister->delete($this->findEntity($entityId));
    }

    public function findEntity(int $id): Notification
    {
        return $this->entityManager->getRepository(Notification::class)->find($id);
    }

    public function findAllEntities(): array
    {
        return $this->entityManager->getRepository(Notification::class)->findAll();
    }

    public function findDocumentByEntity(int $entityId): object
    {
        return $this->documentManager->getRepository(NotificationDocument::class)->findOneBy(["entityId" => $entityId]);
    }
}