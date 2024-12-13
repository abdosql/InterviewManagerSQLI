<?php

namespace App\Transformation\Strategy;

use App\Document\UserDocument;
use App\Entity\Evaluator;
use App\Entity\HRManager;
use App\Entity\User;
use App\Transformation\Strategy\Abstract\AbstractUserTransformationStrategy;
use Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag;

#[AutoconfigureTag("app.transform_to_entity_strategy", ['type' => 'user'])]
#[AutoconfigureTag("app.transform_to_document_strategy", ['type' => 'user'])]

class UserTransformationStrategy extends AbstractUserTransformationStrategy
{

    public function transformToDocument(int $entityId): UserDocument
    {
        $entity = $this->getEntityOrFail($entityId);
        if ($entity instanceof Evaluator) {
            return $this->transformEvaluatorEntityToDocument($entity);
        }
        if ($entity instanceof HRManager) {
            return $this->transformHRManagerEntityToDocument($entity);
        }
        throw new \InvalidArgumentException('Unsupported entity type');
    }


    public function transformToEntity(object $document): User
    {
        if (!$document instanceof UserDocument){
            throw new \InvalidArgumentException("Document must be an instance of UserDocument");
        }
        return $this->getEntityOrFail($document->getEntityId());

    }
}