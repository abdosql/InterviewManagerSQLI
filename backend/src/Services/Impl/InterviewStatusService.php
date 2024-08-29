<?php
/**
 * @author Saqqal Abdelaziz <seqqal.abdelaziz@gmail.com>
 * @Linkedin https://www.linkedin.com/abdelaziz-saqqal
 */

namespace App\Services\Impl;

use App\Document\InterviewStatusDocument;
use App\Entity\InterviewStatus;
use App\Persister\Document\InterviewStatusDocumentPersister;
use App\Persister\DocumentPersisterInterface;
use App\Persister\Entity\InterviewStatusEntityPersister;
use App\Persister\EntityPersisterInterface;
use App\Services\DatabasePersistence\DocumentPersistenceServiceInterface;
use App\Services\DatabasePersistence\EntityPersistenceServiceInterface;
use Doctrine\ODM\MongoDB\DocumentManager;
use Doctrine\ODM\MongoDB\DocumentNotFoundException;
use Doctrine\ODM\MongoDB\MongoDBException;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\DependencyInjection\Attribute\Autowire;

class InterviewStatusService implements DocumentPersistenceServiceInterface, EntityPersistenceServiceInterface
{
    public function __construct(
        #[Autowire(service: InterviewStatusDocumentPersister::class)]
        private DocumentPersisterInterface $documentPersister,
        #[Autowire(service: InterviewStatusEntityPersister::class)]
        private EntityPersisterInterface $entityPersister,
        private DocumentManager $documentManager,
        private EntityManagerInterface $entityManager
    ){}

    /**
     * @param object $document
     */
    public function saveDocument(object $document): void
    {
        if (!$document instanceof InterviewStatusDocument) {
            throw new \InvalidArgumentException("Document must be an instance of InterviewStatusDocument");
        }
        $this->documentPersister->save($document);
    }

    /**
     * @throws MongoDBException
     */
    public function updateDocument(int $id, object $document): void
    {
        if (!$document instanceof InterviewStatusDocument) {
            throw new \InvalidArgumentException("Document must be an instance of InterviewStatusDocument");
        }
        $InterviewStatusDocument = $this->findDocumentByEntity($id);
        if (!$InterviewStatusDocument){
            throw new MongoDBException("InterviewStatus not found");
        }
        $InterviewStatusDocument->setDocument($document);
        $this->documentPersister->update($InterviewStatusDocument);
    }

    /**
     * @throws MongoDBException
     */
    public function deleteDocument(int $entityId): void
    {
        $InterviewStatusDocument = $this->findDocumentByEntity($entityId);
        if (!$InterviewStatusDocument) {
            throw new DocumentNotFoundException("InterviewStatus not found");
        }
        $this->documentPersister->delete($InterviewStatusDocument);
    }

    public function findDocument($id): InterviewStatusDocument
    {
        return $this->documentManager->getRepository(InterviewStatusDocument::class)->findOneBy(["entityId" => $id]);
    }

    public function findAllDocuments(): array
    {
        // TODO: Implement findAllDocuments() method.
    }

    public function saveEntity(object $entity): void
    {
        if (!$entity instanceof InterviewStatus) {
            throw new \InvalidArgumentException("Document must be an instance of InterviewStatusDocument");
        }
        $this->entityPersister->save($entity);
    }

    public function updateEntity(object $entity): void
    {
        if (!$entity instanceof InterviewStatus){
            throw new \InvalidArgumentException("Entity must be an instance of InterviewStatus");
        }
        $this->entityPersister->update($entity);
    }

    public function deleteEntity(int $entityId): void
    {
        $this->entityPersister->delete($this->findEntity($entityId));
    }

    public function findEntity(int $id): InterviewStatus
    {
        return $this->entityManager->getRepository(InterviewStatus::class)->find($id);
    }

    public function findAllEntities(): array
    {
        return $this->entityManager->getRepository(InterviewStatus::class)->findAll();
    }

    public function findDocumentByEntity(int $entityId): object
    {
        return $this->documentManager->getRepository(InterviewStatusDocument::class)->findOneBy(["entityId" => $entityId]);
    }
}