<?php

namespace App\Persister;

use MongoDB\BSON\Document;

interface DocumentPersisterInterface
{
    public function save(object $document, bool $flush = true): void;
    public function update(object $document, bool $flush = true): void;
    public function delete(object $document, bool $flush = true): void;
}