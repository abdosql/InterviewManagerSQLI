<?php
/**
 * @author Saqqal Abdelaziz <seqqal.abdelaziz@gmail.com>
 * @Linkedin https://www.linkedin.com/abdelaziz-saqqal
 */

namespace App\Transformation\Strategy;

use App\Document\InterviewDocument;
use App\Entity\Interview;
use App\Transformation\TransformToDocumentStrategyInterface;
use App\Transformation\TransformToEntityStrategyInterface;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Mapping\MappingException;
use Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag;

#[AutoconfigureTag("app.transform_to_entity_strategy", ['type' => 'interview'])]
#[AutoconfigureTag("app.transform_to_document_strategy", ['type' => 'interview'])]
readonly class InterviewTransformationStrategy implements TransformToDocumentStrategyInterface, TransformToEntityStrategyInterface
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

        return $this->entityManager->getRepository(Interview::class)->find($document->getEntityId());
    }
}