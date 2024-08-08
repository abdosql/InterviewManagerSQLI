<?php

namespace App\Adapter;

use App\Transformation\Factory\TransformationStrategyFactory;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use Symfony\Component\DependencyInjection\Attribute\Autowire;

readonly class DataTransformationAdapter
{
    public function __construct(
        #[Autowire(service: TransformationStrategyFactory::class)]
        private TransformationStrategyFactory $transformationStrategyFactory
    )
    {
    }

    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function transformToDocument($entity, string $type): object
    {
        $strategy = $this->transformationStrategyFactory->getTransformToDocumentStrategy($type);
        return $strategy->transformToDocument($entity);
    }

    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function transformToEntity($document, string $type): object
    {
        $strategy = $this->transformationStrategyFactory->getTransformToEntityStrategy($type);
        return $strategy->transformToEntity($document);
    }
}