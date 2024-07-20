<?php

namespace App\Handler\MessageHandler\CandidateMessageHandlers;

use App\Message\CandidateMessages\CandidateDeletedMessage;
use App\Services\Impl\CandidateService;
use Doctrine\ODM\MongoDB\MongoDBException;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
class CandidateDeleteMessageHandler
{
    public function __construct(
        private CandidateService $candidateService
    )
    {}

    /**
     * @throws MongoDBException
     */
    public function __invoke(CandidateDeletedMessage $message): void
    {
        $this->candidateService->deleteDocument($message->getId());
    }

}