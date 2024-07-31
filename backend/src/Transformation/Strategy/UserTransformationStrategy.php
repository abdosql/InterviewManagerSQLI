<?php

namespace App\Transformation\Strategy;

use App\Document\UserDocument;
use App\Entity\Evaluator;
use App\Entity\HRManager;
use App\Transformation\Strategy\Abstract\AbstractUserTransformationStrategy;

class UserTransformationStrategy extends AbstractUserTransformationStrategy
{

    public function transformToDocument($entityId): object
    {
        $entity = $this->getEntityOrFail($entityId);
        $document = new UserDocument();
        if ($entity instanceof Evaluator){
            $document = $this->transformEvaluatorEntityToDocument($entity);
        }
        if ($document instanceof HRManager){
            $document = $this->transformHRManagerEntityToDocument($entity);
        }
        return $document;
    }


}