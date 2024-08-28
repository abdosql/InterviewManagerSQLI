<?php
/**
 * @author Saqqal Abdelaziz <seqqal.abdelaziz@gmail.com>
 * @Linkedin https://www.linkedin.com/abdelaziz-saqqal
 */

namespace App\Services\Impl;

use App\Document\InterviewDocument;
use App\Entity\Interview;
use App\Persister\Document\InterviewDocumentPersister;
use App\Persister\DocumentPersisterInterface;
use App\Persister\Entity\InterviewEntityPersister;
use App\Persister\EntityPersisterInterface;
use App\Services\DatabasePersistence\DocumentPersistenceServiceInterface;
use App\Services\DatabasePersistence\EntityPersistenceServiceInterface;
use Doctrine\ODM\MongoDB\DocumentManager;
use Doctrine\ODM\MongoDB\DocumentNotFoundException;
use Doctrine\ODM\MongoDB\MongoDBException;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\DependencyInjection\Attribute\Autowire;

readonly class InterviewService implements DocumentPersistenceServiceInterface, EntityPersistenceServiceInterface
{
    public function __construct(
        #[Autowire(service: InterviewDocumentPersister::class)]
        private DocumentPersisterInterface $documentPersister,
        #[Autowire(service: InterviewEntityPersister::class)]
        private EntityPersisterInterface   $entityPersister,
        private DocumentManager            $documentManager,
        private EntityManagerInterface     $entityManager
    ){}

    /**
     * @param object $document
     */
    public function saveDocument(object $document): void
    {
        if (!$document instanceof InterviewDocument) {
            throw new \InvalidArgumentException("Document must be an instance of InterviewDocument");
        }
        $this->documentPersister->save($document);
    }

    /**
     * @throws MongoDBException
     */
    public function updateDocument(int $id, object $document): void
    {
        if (!$document instanceof InterviewDocument) {
            throw new \InvalidArgumentException("Document must be an instance of InterviewDocument");
        }
        $InterviewDocument = $this->findDocumentByEntity($id);
        if (!$InterviewDocument){
            throw new MongoDBException("Interview not found");
        }
        $InterviewDocument->setDocument($document);
        $this->documentPersister->update($InterviewDocument);
    }

    /**
     * @throws MongoDBException
     */
    public function deleteDocument(int $entityId): void
    {
        $InterviewDocument = $this->findDocumentByEntity($entityId);
        if (!$InterviewDocument) {
            throw new DocumentNotFoundException("Interview not found");
        }
        $this->documentPersister->delete($InterviewDocument);
    }

    public function findDocument($id)
    {
        return $this->documentManager->getRepository(Interview::class)->findOneBy(["entityId" => $id]);
    }

    public function findAllDocuments(): array
    {
        // TODO: Implement findAllDocuments() method.
    }

    public function saveEntity(object $entity): void
    {
        if (!$entity instanceof Interview) {
            throw new \InvalidArgumentException("Document must be an instance of InterviewDocument");
        }
        $this->entityPersister->save($entity);
    }

    public function updateEntity(object $entity): void
    {
        if (!$entity instanceof Interview){
            throw new \InvalidArgumentException("Entity must be an instance of Interview");
        }
        $this->entityPersister->update($entity);
    }

    public function deleteEntity(int $entityId): void
    {
        $this->entityPersister->delete($this->findEntity($entityId));
    }

    public function findEntity(int $id): Interview
    {
        return $this->entityManager->getRepository(Interview::class)->find($id);
    }

    public function findAllEntities(): array
    {
        return $this->entityManager->getRepository(Interview::class)->findAll();
    }

    public function findDocumentByEntity(int $entityId): object
    {
        return $this->documentManager->getRepository(Interview::class)->findOneBy(["entityId" => $entityId]);
    }
}