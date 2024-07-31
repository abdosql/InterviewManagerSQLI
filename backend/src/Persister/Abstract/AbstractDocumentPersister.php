<?php

namespace App\Persister\Abstract;

use App\Persister\DocumentPersisterInterface;
use App\Exception\PersistenceException;
use Doctrine\ODM\MongoDB\DocumentManager;
use Doctrine\ODM\MongoDB\MongoDBException;

abstract class AbstractDocumentPersister implements DocumentPersisterInterface
{
    public function __construct(protected DocumentManager $documentManager)
    {
    }

    /**
     * @param object $document
     * @param bool $flush
     * @return void
     * @throws PersistenceException
     */
    public function save(object $document, bool $flush = true): void
    {
        try {
            $this->documentManager->persist($document);
            if ($flush) {
                $this->flush();
            }
        } catch (\Exception $e) {
            throw new PersistenceException("Failed to save document: " . $e->getMessage(), 0, $e, get_class($document), 'save');
        }
    }

    /**
     * @param object $document
     * @param bool $flush
     * @return void
     * @throws PersistenceException
     */
    public function update(object $document, bool $flush = true): void
    {
        try {
            $this->documentManager->merge($document);
            if ($flush) {
                $this->flush();
            }
        } catch (\Exception $e) {
            throw new PersistenceException("Failed to update document: " . $e->getMessage(), 0, $e);
        }
    }

    /**
     * @param object $document
     * @param bool $flush
     * @return void
     * @throws PersistenceException
     */
    public function delete(object $document, bool $flush = true): void
    {
        try {
            $this->documentManager->remove($document);
            if ($flush) {
                $this->flush();
            }
        } catch (\Exception $e) {
            throw new PersistenceException("Failed to delete document: " . $e->getMessage(), 0, $e);
        }
    }

    /**
     * @return void
     * @throws PersistenceException
     */
    protected function flush(): void
    {
        try {
            $this->documentManager->flush();
        } catch (MongoDBException $e) {
            throw new PersistenceException("Failed to flush changes: " . $e->getMessage(), 0, $e);
        }
    }
}