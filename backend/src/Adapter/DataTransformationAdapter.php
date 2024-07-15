<?php

namespace App\Adapter;

use App\Transformation\Factory\TransformationStrategyFactory;

readonly class DataTransformationAdapter
{
    public function __construct(private TransformationStrategyFactory $transformationStrategyFactory)
    {
    }

    public function transformToDocument($entity, string $type): object
    {
        $strategy = $this->transformationStrategyFactory->getTransformToDocumentStrategy($type);
        return $strategy->transformToDocument($entity);
    }
    public function transformToEntity($document, string $type): object
    {
        $strategy = $this->transformationStrategyFactory->getTransformToEntityStrategy($type);
        return $strategy->transformToEntity($document);
    }
}