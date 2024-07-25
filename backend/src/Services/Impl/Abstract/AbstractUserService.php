<?php

namespace App\Services\Impl\Abstract;

use App\Document\UserDocument;
use App\Entity\Evaluator;
use App\Entity\User;
use App\Services\DatabasePersistence\DocumentPersistenceServiceInterface;
use App\Services\DatabasePersistence\EntityPersistenceServiceInterface;
use Doctrine\ODM\MongoDB\DocumentManager;
use Doctrine\ODM\MongoDB\DocumentNotFoundException;
use Doctrine\ODM\MongoDB\MongoDBException;
use Doctrine\ORM\EntityManagerInterface;

class AbstractUserService implements DocumentPersistenceServiceInterface, EntityPersistenceServiceInterface
{
    public function __construct(private DocumentManager $documentManager, private EntityManagerInterface $entityManager)
    {
    }

    /**
     * @throws MongoDBException
     */
    public function saveDocument(object $document): void
    {
        $this->documentManager->persist($document);
        $this->documentManager->flush();
    }

    /**
     * @throws MongoDBException
     */
    public function updateDocument(int $id, object $document): void
    {
        if (!$document instanceof UserDocument) {
            throw new \InvalidArgumentException("Document must be an instance of UserDocument");
        }
        $UserDocument = $this->findDocumentByEntity($id);
        if (!$UserDocument){
            throw new MongoDBException("User not found");
        }
        $UserDocument->setDocument($document);
        $this->documentManager->flush();
    }

    /**
     * @throws MongoDBException
     * @throws \Exception
     */
    public function deleteDocument(int $entityId): void
    {
        $userDocument = $this->findDocumentByEntity($entityId);
        if (!$userDocument) {
            throw new DocumentNotFoundException("Document not found");
        }
        /*
         * we need to handle the deletion of the document association with other documents
         */
        $this->documentManager->remove($userDocument);
        $this->documentManager->flush();
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
        $this->entityManager->persist($entity);
        $this->entityManager->flush();
    }

    public function updateEntity(object $entity): void
    {
        if (!$entity instanceof User){
            throw new \InvalidArgumentException("Entity must be an instance of User");
        }
        $this->entityManager->flush();
    }

    public function deleteEntity(int $entityId): void
    {
        $this->entityManager->remove($this->findEntity($entityId));
        $this->entityManager->flush();
    }

    public function findEntity(int $id): User
    {
        return $this->entityManager->getRepository(Evaluator::class)->find($id);
    }

    public function findAllEntities(): array
    {
        return $this->entityManager->getRepository(Evaluator::class)->findAll();
    }

    public function findDocumentByEntity(int $entityId): object
    {
        return $this->documentManager->getRepository(UserDocument::class)->findOneBy(["entityId" => $entityId]);
    }
}