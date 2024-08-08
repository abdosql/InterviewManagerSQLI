<?php
/**
 * @author Saqqal Abdelaziz <seqqal.abdelaziz@gmail.com>
 * @Linkedin https://www.linkedin.com/abdelaziz-saqqal
 */

namespace App\Transformation\Strategy;

use App\Document\InterviewDocument;
use App\Entity\Interview;
use App\Transformation\TransformToDocumentStrategyInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag;

#[AutoconfigureTag("app.transform_to_entity_strategy", ['type' => 'interview'])]
#[AutoconfigureTag("app.transform_to_document_strategy", ['type' => 'interview'])]
readonly class InterviewTransformationStrategy implements TransformToDocumentStrategyInterface
{
    public function __construct(private EntityManagerInterface $entityManager)
    {
    }
    public function transformToDocument(int $entityId): InterviewDocument
    {
        $entity = $this->entityManager->getRepository(Interview::class)->find($entityId);
        if (null === $entity) {
            throw new \RuntimeException("Interview not found with id: $entityId");
        }

        $interviewDocument = new InterviewDocument();
        $interviewDocument
            ->setInterviewDate($entity->getInterviewDate())
            ->setCandidate($entity->getCandidate())
            ->setInterviewLocation($entity->getInterviewLocation())
            ->setEvaluator($entity->getEvaluator())
        ;
        return $interviewDocument;
    }
}