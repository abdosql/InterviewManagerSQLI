<?php

namespace App\Transformation\Strategy;

use App\Document\EvaluatorDocument;
use App\Entity\Evaluator;
use App\Transformation\TransformToDocumentStrategyInterface;

class EvaluatorTransformationStrategy implements TransformToDocumentStrategyInterface
{

    public function transformToDocument($entity): object
    {
        if (!$entity instanceof Evaluator) {
            throw new \InvalidArgumentException('Expected entity to be an instance of Evaluator');
        }

        $document = new EvaluatorDocument();
        $document->setFirstName($entity->getFirstName());
        $document->setLastName($entity->getLastName());
        $document->setEmail($entity->getEmail());
        $document->setPhone($entity->getPhone());
        $document->setEntityId($entity->getId());
        $document->setUsername($entity->getUsername());
        $document->setPassword($entity->getPassword());
        $document->setRoles($entity->getRoles());
        $document->setSpecialization($entity->getSpecialization());

        return $document;
    }
}