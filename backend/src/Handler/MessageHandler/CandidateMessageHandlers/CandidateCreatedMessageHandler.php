<?php
declare(strict_types=1);


namespace App\Handler\MessageHandler\CandidateMessageHandlers;

use App\Adapter\DataTransformationAdapter;
use App\Entity\Candidate;
use App\Message\CandidateMessages\CandidateCreatedMessage;
use App\Services\Impl\CandidateService;
use Doctrine\ODM\MongoDB\MongoDBException;
use ProxyManager\Exception\ExceptionInterface;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
class CandidateCreatedMessageHandler
{
    public function __construct(
        private DataTransformationAdapter $transformationAdapter,
        private CandidateService          $candidateService
    )
    {}

    /**
     * @param CandidateCreatedMessage $message
     * @throws MongoDBException
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function __invoke(CandidateCreatedMessage $message): void
    {
        $candidateEntity = $this->getEntityFromMessage($message);
        $candidateDocument = $this->transformationAdapter->transformToDocument($candidateEntity, 'candidate');
        $this->candidateService->saveDocument($candidateDocument);
    }

    public function getEntityFromMessage(CandidateCreatedMessage $message): Candidate
    {
        return $this->candidateService->findEntity($message->getId());
    }
}