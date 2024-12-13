<?php
/**
 * @author Saqqal Abdelaziz <seqqal.abdelaziz@gmail.com>
 * @Linkedin https://www.linkedin.com/abdelaziz-saqqal
 */

namespace App\Transformation\Strategy;

use App\Document\AppreciationDocument;
use App\Entity\Appreciation;
use App\Services\Impl\AppreciationService;
use App\Services\Impl\InterviewService;
use App\Transformation\TransformToDocumentStrategyInterface;
use App\Transformation\TransformToEntityStrategyInterface;
use Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag;

#[AutoconfigureTag("app.transform_to_entity_strategy", ['type' => 'appreciation'])]
#[AutoconfigureTag("app.transform_to_document_strategy", ['type' => 'appreciation'])]
readonly class AppreciationTransformationStrategy implements TransformToDocumentStrategyInterface, TransformToEntityStrategyInterface
{
    public function __construct(private AppreciationService $appreciationService, private InterviewService $interviewService)
    {
    }
    public function transformToDocument(int $entityId): AppreciationDocument
    {
        $entity = $this->appreciationService->findEntity($entityId);

        $appreciationDocument = new AppreciationDocument();
        
        $interviewDocument = $this->interviewService->findDocument($entity->getInterview()->getId());

        $appreciationDocument
            ->setInterview($interviewDocument)
            ->setComment($entity->getComment())
            ->setScore($entity->getScore())
            ->setEntityId($entityId)
        ;
        return $appreciationDocument;
    }

    /**
     * @param object $document
     * @return Appreciation
     */
    public function transformToEntity(object $document): Appreciation
    {
        if (!$document instanceof AppreciationDocument){
            throw new \InvalidArgumentException("Document must be an instance of AppreciationDocument");
        }

        return $this->appreciationService->findEntity($document->getEntityId());
    }
}