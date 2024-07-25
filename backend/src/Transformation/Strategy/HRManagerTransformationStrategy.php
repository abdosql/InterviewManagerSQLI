<?php

namespace App\Transformation\Strategy;

use App\Document\HRManagerDocument;
use App\Entity\HRManager;
use App\Transformation\Strategy\Abstract\AbstractUserTransformationStrategy;

class HRManagerTransformationStrategy extends AbstractUserTransformationStrategy
{

    public function transformToDocument($entity): object
    {
        if (!$entity instanceof HRManager) {
            throw new \InvalidArgumentException('Expected entity to be an instance of Evaluator');
        }

        $document = new HRManagerDocument();
        $this->transformCommonFields($entity, $document);
        $document->setDepartment($entity->getDepartment());
        $document->setPosition($entity->getPosition());

        return $document;
    }
}