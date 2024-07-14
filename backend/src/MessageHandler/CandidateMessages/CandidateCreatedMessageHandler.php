<?php

namespace App\MessageHandler\CandidateMessages;

use App\Message\CandidateMessages\CandidateCreatedMessage;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
class CandidateCreatedMessageHandler
{

    public function __invoke(CandidateCreatedMessage $message): void
    {
        dd($message);
    }
}