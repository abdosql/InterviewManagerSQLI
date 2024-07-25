<?php

namespace App\Transformation\Strategy;

use App\Document\EvaluatorDocument;
use App\Entity\Evaluator;
use App\Transformation\Strategy\Abstract\AbstractUserTransformationStrategy;
use App\Transformation\TransformToDocumentStrategyInterface;

class EvaluatorTransformationStrategy extends AbstractUserTransformationStrategy
{

    public function transformToDocument($entity): object
    {
        if (!$entity instanceof Evaluator) {
            throw new \InvalidArgumentException('Expected entity to be an instance of Evaluator');
        }

        $document = new EvaluatorDocument();
        $this->transformCommonFields($entity, $document);
        $document->setSpecialization($entity->getSpecialization());

        return $document;
    }
}