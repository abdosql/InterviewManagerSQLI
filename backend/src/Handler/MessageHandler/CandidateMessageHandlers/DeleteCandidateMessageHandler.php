<?php

namespace App\Handler\MessageHandler\CandidateMessageHandlers;

use App\Message\CandidateMessages\CandidateUpdatedMessage;
use App\Services\Impl\CandidateService;
use Doctrine\ODM\MongoDB\MongoDBException;

class DeleteCandidateMessageHandler
{
    public function __construct(
        private CandidateService $candidateService
    )
    {}

    /**
     * @throws MongoDBException
     */
    public function __invoke(CandidateUpdatedMessage $message): void
    {
        $this->candidateService->deleteDocument($message->getId());
    }

}