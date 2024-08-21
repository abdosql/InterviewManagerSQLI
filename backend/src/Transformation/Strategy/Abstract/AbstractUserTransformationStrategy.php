<?php

namespace App\Transformation\Strategy\Abstract;

use App\Document\EvaluatorDocument;
use App\Document\HRManagerDocument;
use App\Document\UserDocument;
use App\Entity\Evaluator;
use App\Entity\HRManager;
use App\Entity\User;
use App\Transformation\TransformToDocumentStrategyInterface;
use App\Transformation\TransformToEntityStrategyInterface;
use Doctrine\ORM\EntityManagerInterface;

abstract class AbstractUserTransformationStrategy implements TransformToDocumentStrategyInterface, TransformToEntityStrategyInterface
{
    public function __construct(private readonly EntityManagerInterface $entityManager)
    {
    }
    /**
     * @param User $entity
     * @param UserDocument $document
     * @return void
     */
    protected function transformCommonFields(User $entity, UserDocument $document): void
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
    public function getEntityOrFail(int $entityId): User
    {
        $entity = $this->entityManager->getRepository(User::class)->find($entityId);
        if (null === $entity) {
            throw new \RuntimeException("User not found with id: $entityId");
        }
        return $entity;
    }
    public function transformEvaluatorEntityToDocument(User $user): EvaluatorDocument
    {
        if (!$user instanceof Evaluator){
            throw new \InvalidArgumentException('Expected entity to be an instance of Evaluator');
        }
        $document = new EvaluatorDocument();
        $this->transformCommonFields($user, $document);
        $document->setSpecialization($user->getSpecialization());

        return $document;
    }

    /**
     * @param User $user
     * @return HRManagerDocument
     */
    public function transformHRManagerEntityToDocument(User $user): HRManagerDocument
    {
        if (!$user instanceof HRManager){
            throw new \InvalidArgumentException('Expected entity to be an instance of Evaluator');
        }
        $document = new HRManagerDocument();
        $this->transformCommonFields($user, $document);
        $document->setDepartment($user->getDepartment());
        $document->setPosition($user->getPosition());

        return $document;
    }
}