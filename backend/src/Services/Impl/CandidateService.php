<?php
declare(strict_types=1);
namespace App\Services\Impl;

use App\Document\CandidateDocument;
use App\Entity\Candidate;
use App\Services\DatabasePersistence\DocumentPersistenceServiceInterface;
use App\Services\DatabasePersistence\EntityPersistenceServiceInterface;
use Doctrine\ODM\MongoDB\DocumentManager;
use Doctrine\ODM\MongoDB\MongoDBException;
use Doctrine\ORM\EntityManagerInterface;

class CandidateService implements DocumentPersistenceServiceInterface, EntityPersistenceServiceInterface
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
        if (!$document instanceof CandidateDocument) {
            throw new \InvalidArgumentException("Document must be an instance of CandidateDocument");
        }
        $candidateDocument = $this->findDocumentByEntity($id);
        if (!$candidateDocument){
            throw new MongoDBException("Candidate not found");
        }
        $candidateDocument->setDocument($document);
        $this->documentManager->flush();
    }

    /**
     * @throws MongoDBException
     */
    public function deleteDocument(int $entityId): void
    {
        $candidateDocument = $this->findDocumentByEntity($entityId);
        if (!$candidateDocument) {
            throw new \Exception('Candidate not found');
        }
        $this->documentManager->remove($candidateDocument->getResume());
        foreach ($candidateDocument->getInterviews() as $interview) {
            $this->documentManager->remove($interview);
        }

        foreach ($candidateDocument->getCandidatePhases() as $candidatePhase) {
            $this->documentManager->remove($candidatePhase);
        }

        $this->documentManager->remove($candidateDocument);
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
        if (!$entity instanceof Candidate){
            throw new \InvalidArgumentException("Entity must be an instance of Candidate");
        }
        $this->entityManager->flush();
    }

    public function deleteEntity(int $entityId): void
    {
        $this->entityManager->remove($this->findEntity($entityId));
        $this->entityManager->flush();
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