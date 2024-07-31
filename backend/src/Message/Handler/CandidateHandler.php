<?php
/**
 * @author Saqqal Abdelaziz <seqqal.abdelaziz@gmail.com>
 * @Linkedin https://www.linkedin.com/abdelaziz-saqqal
 */

namespace App\Message\Handler;

use App\Adapter\DataTransformationAdapter;
use App\Message\Abstract\AbstractCandidateMessage;
use App\Message\Candidate\CandidateCreatedMessage;
use App\Message\Candidate\CandidateDeletedMessage;
use App\Message\Candidate\CandidateUpdatedMessage;
use App\Services\Impl\CandidateService;
use Doctrine\ODM\MongoDB\MongoDBException;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Psr\Log\LoggerInterface;

#[AsMessageHandler]
class CandidateHandler
{
    public function __construct(
        private DataTransformationAdapter $transformationAdapter,
        private CandidateService $candidateService,
        private LoggerInterface $logger
    ) {}

    /**
     * @param AbstractCandidateMessage $message
     * @throws ContainerExceptionInterface
     * @throws MongoDBException
     * @throws NotFoundExceptionInterface
     */
    public function __invoke(AbstractCandidateMessage $message): void
    {
        try {
            if ($message instanceof CandidateCreatedMessage) {
                $this->handleNewCandidate($message);
            } elseif ($message instanceof CandidateUpdatedMessage) {
                $this->handleUpdatedCandidate($message);
            } elseif ($message instanceof CandidateDeletedMessage) {
                $this->handleDeletedCandidate($message);
            } else {
                throw new \InvalidArgumentException('Unsupported message type');
            }
        } catch (\Exception $e) {
            $this->logger->error('Error processing candidate message: ' . $e->getMessage(), [
                'message_type' => get_class($message),
                'candidate_id' => $message->getId(),
            ]);
            throw $e;
        }
    }

    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     * @throws MongoDBException
     */
    private function handleNewCandidate(CandidateCreatedMessage $message): void
    {
        $candidateDocument = $this->transformationAdapter->transformToDocument($message->getId(), 'candidate');
        $this->candidateService->saveDocument($candidateDocument);
    }

    /**
     * @throws ContainerExceptionInterface
     * @throws MongoDBException
     * @throws NotFoundExceptionInterface
     */
    private function handleUpdatedCandidate(CandidateUpdatedMessage $message): void
    {
        $updatedCandidateDocument = $this->transformationAdapter->transformToDocument($message->getId(), 'candidate');
        $this->candidateService->updateDocument($message->getId(), $updatedCandidateDocument);
    }

    /**
     * @throws MongoDBException
     */
    private function handleDeletedCandidate(CandidateDeletedMessage $message): void
    {
        $this->candidateService->deleteDocument($message->getId());
    }
}