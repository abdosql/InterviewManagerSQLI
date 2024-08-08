<?php
declare(strict_types=1);
namespace App\Services\Impl;

use App\Document\CandidateDocument;
use App\Entity\Candidate;
use App\Persister\Document\CandidateDocumentPersister;
use App\Persister\DocumentPersisterInterface;
use App\Persister\Entity\CandidateEntityPersister;
use App\Persister\EntityPersisterInterface;
use App\Services\DatabasePersistence\DocumentPersistenceServiceInterface;
use App\Services\DatabasePersistence\EntityPersistenceServiceInterface;
use Doctrine\ODM\MongoDB\DocumentManager;
use Doctrine\ODM\MongoDB\DocumentNotFoundException;
use Doctrine\ODM\MongoDB\MongoDBException;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\DependencyInjection\Attribute\Autowire;

final class CandidateService implements DocumentPersistenceServiceInterface, EntityPersistenceServiceInterface
{
    public function __construct(
        #[Autowire(service: CandidateDocumentPersister::class)]
        private DocumentPersisterInterface $documentPersister,
        #[Autowire(service: CandidateEntityPersister::class)]
        private EntityPersisterInterface $entityPersister,
        private DocumentManager $documentManager,
        private EntityManagerInterface $entityManager
    ){}

    /**
     * @param object $document
     */
    public function saveDocument(object $document): void
    {
        if (!$document instanceof CandidateDocument) {
            throw new \InvalidArgumentException("Document must be an instance of CandidateDocument");
        }
        $this->documentPersister->save($document);
    }

    /**
     * @throws MongoDBException
     */
    public function updateDocument(int $id, object $document): void
    {
        if (!$document instanceof CandidateDocument) {
            throw new \InvalidArgumentException("Document must be an instance of CandidateDocument");
        }
        $candidateDocument = $this->findDocumentByEntity($id);
        if (!$candidateDocument){
            throw new MongoDBException("Candidate not found");
        }
        $candidateDocument->setDocument($document);
        $this->documentPersister->update($candidateDocument);
    }

    /**
     * @throws MongoDBException
     */
    public function deleteDocument(int $entityId): void
    {
        $candidateDocument = $this->findDocumentByEntity($entityId);
        if (!$candidateDocument) {
            throw new DocumentNotFoundException("Candidate not found");
        }
        $this->documentPersister->delete($candidateDocument);
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
        if (!$entity instanceof Candidate) {
            throw new \InvalidArgumentException("Document must be an instance of CandidateDocument");
        }
        $this->entityPersister->save($entity);
    }

    public function updateEntity(object $entity): void
    {
        if (!$entity instanceof Candidate){
            throw new \InvalidArgumentException("Entity must be an instance of Candidate");
        }
        $this->entityPersister->update($entity);
    }

    public function deleteEntity(int $entityId): void
    {
        $this->entityPersister->delete($this->findEntity($entityId));
    }

    public function findEntity(int $id): Candidate
    {
        return $this->entityManager->getRepository(Candidate::class)->find($id);
    }

    public function findAllEntities(): array
    {
        return $this->entityManager->getRepository(Candidate::class)->findAll();
    }

    public function findDocumentByEntity(int $entityId): object
    {
        return $this->documentManager->getRepository(CandidateDocument::class)->findOneBy(["entityId" => $entityId]);
    }
}