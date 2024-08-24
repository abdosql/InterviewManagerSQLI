<?php

namespace App\Services\Impl\Abstract;

use App\Document\UserDocument;
use App\Entity\User;
use App\Persister\Document\UserDocumentPersister;
use App\Persister\DocumentPersisterInterface;
use App\Persister\Entity\UserEntityPersister;
use App\Persister\EntityPersisterInterface;
use App\Services\DatabasePersistence\DocumentPersistenceServiceInterface;
use App\Services\DatabasePersistence\EntityPersistenceServiceInterface;
use Doctrine\ODM\MongoDB\DocumentManager;
use Doctrine\ODM\MongoDB\DocumentNotFoundException;
use Doctrine\ODM\MongoDB\MongoDBException;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\DependencyInjection\Attribute\Autowire;

class AbstractUserService implements DocumentPersistenceServiceInterface, EntityPersistenceServiceInterface
{
    public function __construct(
        #[Autowire(service: UserDocumentPersister::class)]
        private DocumentPersisterInterface $documentPersister,
        #[Autowire(service: UserEntityPersister::class)]
        private EntityPersisterInterface $entityPersister,
        private DocumentManager $documentManager,
        private EntityManagerInterface $entityManager
    ){}

    /**
     * @param object $document
     */
    public function saveDocument(object $document): void
    {
        if (!$document instanceof UserDocument) {
            throw new \InvalidArgumentException("Entity must be an instance of User");
        }
        $this->documentPersister->save($document);
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
        $this->documentPersister->update($UserDocument);
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
         * we need to handle the deletion of the document association with other documents apré nchelh
         */
        $this->documentPersister->delete($userDocument);

    }

    public function findDocument($id)
    {
        return $this->documentManager->getRepository(UserDocument::class)->findOneBy(["entityId", $id]);
    }

    public function findAllDocuments(): array
    {
        // TODO: Implement findAllDocuments() method.
    }

    public function saveEntity(object $entity): void
    {
        if (!$entity instanceof User){
            throw new \InvalidArgumentException("Entity must be an instance of User");
        }
        $this->entityPersister->save($entity);
    }

    public function updateEntity(object $entity): void
    {
        if (!$entity instanceof User){
            throw new \InvalidArgumentException("Entity must be an instance of User");
        }
        $this->entityPersister->update($entity);
    }

    public function deleteEntity(int $entityId): void
    {
        $this->entityPersister->delete($this->findEntity($entityId));

    }

    public function findEntity(int $id): User
    {
        return $this->entityManager->getRepository(User::class)->find($id);
    }

    public function findAllEntities(): array
    {
        return $this->entityManager->getRepository(User::class)->findAll();
    }

    public function findDocumentByEntity(int $entityId): object
    {
        return $this->documentManager->getRepository(UserDocument::class)->findOneBy(["entityId" => $entityId]);
    }
}