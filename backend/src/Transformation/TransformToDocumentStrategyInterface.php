<?php

namespace App\Transformation;

interface TransformToDocumentStrategyInterface
{
    public function transformToDocument(int $entityId): mixed;
}