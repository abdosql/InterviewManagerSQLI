<?php

namespace App\Services\DatabasePersistence;

interface EntityPersistenceServiceInterface
{
    public function saveEntity(object $entity): void;
    public function updateEntity(object $entity): void;
    public function deleteEntity(int $entityId): void;
    public function findEntity(int $id): object;
    public function findAllEntities(): array;
}