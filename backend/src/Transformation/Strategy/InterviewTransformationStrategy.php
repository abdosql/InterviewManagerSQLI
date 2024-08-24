<?php
/**
 * @author Saqqal Abdelaziz <seqqal.abdelaziz@gmail.com>
 * @Linkedin https://www.linkedin.com/abdelaziz-saqqal
 */

namespace App\Transformation\Strategy;

use App\Document\InterviewDocument;
use App\Entity\Interview;
use App\Services\Impl\InterviewService;
use App\Transformation\TransformToDocumentStrategyInterface;
use App\Transformation\TransformToEntityStrategyInterface;
use Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag;

#[AutoconfigureTag("app.transform_to_entity_strategy", ['type' => 'interview'])]
#[AutoconfigureTag("app.transform_to_document_strategy", ['type' => 'interview'])]
readonly class InterviewTransformationStrategy implements TransformToDocumentStrategyInterface, TransformToEntityStrategyInterface
{
    public function __construct(private InterviewService $interviewService)
    {
    }
    public function transformToDocument(int $entityId): InterviewDocument
    {
        $entity = $this->interviewService->findEntity($entityId);

        $interviewDocument = new InterviewDocument();
        $interviewDocument
            ->setInterviewDate($entity->getInterviewDate())
            ->setInterviewLocation($entity->getInterviewLocation())
            ->setEntityId($entity->getId());
        ;
        return $interviewDocument;
    }

    /**
     * @param object $document
     * @return Interview
     */
    public function transformToEntity(object $document): Interview
    {
        if (!$document instanceof InterviewDocument){
            throw new \InvalidArgumentException("Document must be an instance of InterviewDocument");
        }

        return $this->interviewService->findEntity($document->getEntityId());
    }
}