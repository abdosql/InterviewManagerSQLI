<?php

namespace App\Transformation\Strategy\Abstract;

use App\Transformation\TransformToDocumentStrategyInterface;

abstract class AbstractUserTransformationStrategy implements TransformToDocumentStrategyInterface
{
    protected function transformCommonFields($entity, $document): void
    {
        $document->setFirstName($entity->getFirstName());
        $document->setLastName($entity->getLastName());
        $document->setEmail($entity->getEmail());
        $document->setPhone($entity->getPhone());
        $document->setEntityId($entity->getId());
        $document->setUsername($entity->getUsername());
        $document->setPassword($entity->getPassword());
        $document->setRoles($entity->getRoles());
    }
}