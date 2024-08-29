<?php
/**
 * @author Saqqal Abdelaziz <seqqal.abdelaziz@gmail.com>
 * @Linkedin https://www.linkedin.com/abdelaziz-saqqal
 */

namespace App\Transformation\Strategy;


use App\Document\InterviewDocument;
use App\Document\InterviewStatusDocument;
use App\Entity\Interview;
use App\Entity\InterviewStatus;
use App\Services\Impl\CandidateService;
use App\Services\Impl\InterviewService;
use App\Services\Impl\InterviewStatusService;
use App\Services\Impl\UserService;
use App\Transformation\TransformToDocumentStrategyInterface;
use App\Transformation\TransformToEntityStrategyInterface;
use Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag;

#[AutoconfigureTag("app.transform_to_entity_strategy", ['type' => 'interviewStatus'])]
#[AutoconfigureTag("app.transform_to_document_strategy", ['type' => 'interviewStatus'])]
readonly class InterviewStatusTransformationStrategy implements TransformToDocumentStrategyInterface, TransformToEntityStrategyInterface
{
    public function __construct(private InterviewStatusService $interviewStatusService, private InterviewService $interviewService)
    {
    }
    public function transformToDocument(int $entityId): InterviewStatusDocument
    {
        $entity = $this->interviewStatusService->findEntity($entityId);
        $interviewDocument = $this->interviewService->findDocument($entity->getInterview()->getId());
        $interviewStatusDocument = new InterviewStatusDocument();
        $interviewStatusDocument
            ->setStatus($entity->getStatus())
            ->setEntityId($entityId)
            ->setInterview($interviewDocument)
            ->setStatusDate($entity->getStatusDate())
            ;

        return $interviewStatusDocument;
    }

    /**
     * @param object $document
     * @return InterviewStatus
     */
    public function transformToEntity(object $document): InterviewStatus
    {
        if (!$document instanceof InterviewStatusDocument){
            throw new \InvalidArgumentException("Document must be an instance of InterviewStatusDocument");
        }

        return $this->interviewStatusService->findEntity($document->getEntityId());
    }
}