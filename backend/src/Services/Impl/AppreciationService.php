<?php
/**
 * @author Saqqal Abdelaziz <seqqal.abdelaziz@gmail.com>
 * @Linkedin https://www.linkedin.com/abdelaziz-saqqal
 */

namespace App\Services\Impl;

use App\Document\AppreciationDocument;
use App\Entity\Appreciation;
use App\Persister\Document\AppreciationDocumentPersister;
use App\Persister\DocumentPersisterInterface;
use App\Persister\Entity\AppreciationEntityPersister;
use App\Persister\EntityPersisterInterface;
use App\Services\DatabasePersistence\DocumentPersistenceServiceInterface;
use App\Services\DatabasePersistence\EntityPersistenceServiceInterface;
use Doctrine\ODM\MongoDB\DocumentManager;
use Doctrine\ODM\MongoDB\DocumentNotFoundException;
use Doctrine\ODM\MongoDB\MongoDBException;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\DependencyInjection\Attribute\Autowire;

class AppreciationService implements DocumentPersistenceServiceInterface, EntityPersistenceServiceInterface
{
    public function __construct(
        #[Autowire(service: AppreciationDocumentPersister::class)]
        private DocumentPersisterInterface $documentPersister,
        #[Autowire(service: AppreciationEntityPersister::class)]
        private EntityPersisterInterface $entityPersister,
        private DocumentManager $documentManager,
        private EntityManagerInterface $entityManager
    ){}

    /**
     * @param object $document
     */
    public function saveDocument(object $document): void
    {
        if (!$document instanceof AppreciationDocument) {
            throw new \InvalidArgumentException("Document must be an instance of AppreciationDocument");
        }
        $this->documentPersister->save($document);
    }

    /**
     * @throws MongoDBException
     */
    public function updateDocument(int $id, object $document): void
    {
        if (!$document instanceof AppreciationDocument) {
            throw new \InvalidArgumentException("Document must be an instance of AppreciationDocument");
        }
        $appreciationDocument = $this->findDocumentByEntity($id);
        if (!$appreciationDocument){
            throw new MongoDBException("Appreciation not found");
        }
        $appreciationDocument->setDocument($document);
        $this->documentPersister->update($appreciationDocument);
    }

    /**
     * @throws MongoDBException
     */
    public function deleteDocument(int $entityId): void
    {
        $appreciationDocument = $this->findDocumentByEntity($entityId);
        if (!$appreciationDocument) {
            throw new DocumentNotFoundException("Appreciation not found");
        }
        $this->documentPersister->delete($appreciationDocument);
    }

    public function findDocument($id): AppreciationDocument
    {
        return $this->documentManager->getRepository(AppreciationDocument::class)->findOneBy(["entityId" => $id]);
    }

    public function findAllDocuments(): array
    {
        // TODO: Implement findAllDocuments() method.
    }

    public function saveEntity(object $entity): void
    {
        if (!$entity instanceof Appreciation) {
            throw new \InvalidArgumentException("Document must be an instance of AppreciationDocument");
        }
        $this->entityPersister->save($entity);
    }

    public function updateEntity(object $entity): void
    {
        if (!$entity instanceof Appreciation){
            throw new \InvalidArgumentException("Entity must be an instance of Appreciation");
        }
        $this->entityPersister->update($entity);
    }

    public function deleteEntity(int $entityId): void
    {
        $this->entityPersister->delete($this->findEntity($entityId));
    }

    public function findEntity(int $id): Appreciation
    {
        return $this->entityManager->getRepository(Appreciation::class)->find($id);
    }

    public function findAllEntities(): array
    {
        return $this->entityManager->getRepository(Appreciation::class)->findAll();
    }

    public function findDocumentByEntity(int $entityId): object
    {
        return $this->documentManager->getRepository(AppreciationDocument::class)->findOneBy(["entityId" => $entityId]);
    }
}