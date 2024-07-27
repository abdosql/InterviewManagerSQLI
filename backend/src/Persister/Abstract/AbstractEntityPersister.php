<?php

namespace App\Persister\Abstract;

use App\Persister\EntityPersisterInterface;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\OptimisticLockException;
use App\Exception\PersistenceException;

abstract class AbstractEntityPersister implements EntityPersisterInterface
{
    public function __construct(protected EntityManagerInterface $entityManager)
    {
    }

    /**
     * @param object $entity
     * @param bool $flush
     * @return void
     * @throws PersistenceException
     */
    public function save(object $entity, bool $flush = true): void
    {
        try {
            $this->entityManager->persist($entity);
            if ($flush) {
                $this->flush();
            }
        } catch (\Exception $e) {
            throw new PersistenceException("Failed to save entity: " . $e->getMessage(), 0, $e);
        }
    }

    /**
     * @param object $entity
     * @param bool $flush
     * @return void
     * @throws PersistenceException
     */
    public function update(object $entity, bool $flush = true): void
    {
        try {
            $this->entityManager->persist($entity);
            if ($flush) {
                $this->flush();
            }
        } catch (\Exception $e) {
            throw new PersistenceException("Failed to update entity: " . $e->getMessage(), 0, $e);
        }
    }

    /**
     * @param object $entity
     * @param bool $flush
     * @return void
     * @throws PersistenceException
     */
    public function delete(object $entity, bool $flush = true): void
    {
        try {
            $this->entityManager->remove($entity);
            if ($flush) {
                $this->flush();
            }
        } catch (\Exception $e) {
            throw new PersistenceException("Failed to delete entity: " . $e->getMessage(), 0, $e);
        }
    }

    /**
     * @return void
     * @throws PersistenceException
     */
    protected function flush(): void
    {
        try {
            $this->entityManager->flush();
        } catch (OptimisticLockException $e) {
            throw new PersistenceException("Optimistic lock exception: " . $e->getMessage(), 0, $e);
        } catch (\Exception $e) {
            throw new PersistenceException("Failed to flush changes: " . $e->getMessage(), 0, $e);
        }
    }

}