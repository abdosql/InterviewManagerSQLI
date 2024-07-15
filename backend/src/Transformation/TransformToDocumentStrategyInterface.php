<?php

namespace App\Transformation;

interface TransformToDocumentStrategyInterface
{
    public function transformToDocument($entity): object;
}