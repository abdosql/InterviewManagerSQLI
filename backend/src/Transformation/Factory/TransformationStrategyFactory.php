<?php

namespace App\Transformation\Factory;

use App\Transformation\TransformToDocumentStrategyInterface;
use App\Transformation\TransformToEntityStrategyInterface;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use Symfony\Component\DependencyInjection\ServiceLocator;


class TransformationStrategyFactory
{
    public function __construct(
        private ServiceLocator $documentStrategies,
        private ServiceLocator $entityStrategies
    ){}

    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function getTransformToEntityStrategy($type): TransformToEntityStrategyInterface
    {
        if (!$this->entityStrategies->has($type)){
            throw new \InvalidArgumentException('Unknown transformation type: ' . $type);
        }
        return $this->entityStrategies->get($type);
    }
    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function getTransformToDocumentStrategy($type): TransformToDocumentStrategyInterface
    {
        if (!$this->documentStrategies->has($type)){
            throw new \InvalidArgumentException('Unknown transformation type: ' . $type);
        }
        return $this->documentStrategies->get($type);
    }
}