<?php
/**
 * @author Saqqal Abdelaziz <seqqal.abdelaziz@gmail.com>
 * @Linkedin https://www.linkedin.com/abdelaziz-saqqal
 */

namespace App\Transformation\Strategy;

use App\Document\InterviewDocument;
use App\Entity\Candidate;
use App\Entity\Interview;
use App\Services\Impl\CandidateService;
use App\Services\Impl\InterviewService;
use App\Services\Impl\UserService;
use App\Transformation\TransformToDocumentStrategyInterface;
use App\Transformation\TransformToEntityStrategyInterface;
use Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag;

#[AutoconfigureTag("app.transform_to_entity_strategy", ['type' => 'interview'])]
#[AutoconfigureTag("app.transform_to_document_strategy", ['type' => 'interview'])]
readonly class InterviewTransformationStrategy implements TransformToDocumentStrategyInterface, TransformToEntityStrategyInterface
{
    public function __construct(private InterviewService $interviewService, private UserService $userService, private CandidateService $candidateService)
    {
    }
    public function transformToDocument(int $entityId): InterviewDocument
    {
        $entity = $this->interviewService->findEntity($entityId);
        $hrManagerDocument = $this->userService->findDocument($entity->getHrManager()->getId());
        $candidateDocument = $this->candidateService->findDocument($entity->getCandidate()->getId());
        $interviewDocument = new InterviewDocument();
        $interviewDocument
            ->setInterviewDate($entity->getInterviewDate())
            ->setInterviewLocation($entity->getInterviewLocation())
            ->setEntityId($entity->getId())
            ->setHrManager($hrManagerDocument)
            ->setCandidate($candidateDocument)
        ;
        foreach ($entity->getEvaluators() as $evaluator)
        {
            $evaluatorDocument = $this->userService->findDocument($evaluator->getId());
            $interviewDocument->addEvaluator($evaluatorDocument);
        }
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