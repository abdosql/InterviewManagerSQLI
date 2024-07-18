<?php
declare(strict_types=1);
namespace App\Services\DatabasePersistence;

interface DocumentPersistenceServiceInterface
{
    public function saveDocument(object $document): void;
    public function updateDocument(int $id, object $document): void;
    public function deleteDocument($id): void;
    public function findDocumentByEntity(int $entityId): object;
    public function findDocument($id);
    public function findAllDocuments(): array;
}