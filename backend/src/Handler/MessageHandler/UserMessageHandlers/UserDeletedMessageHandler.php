<?php

namespace App\Handler\MessageHandler\UserMessageHandlers;

use App\Message\UserMessages\UserCreatedMessage;
use App\Message\UserMessages\UserDeletedMessage;
use App\Services\Impl\UserService;
use Doctrine\ODM\MongoDB\MongoDBException;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
class UserDeletedMessageHandler
{
    public function __construct(
        private UserService $userService,
    )
    {}

    /**
     * @param UserDeletedMessage $message
     * @throws MongoDBException
     */
    public function __invoke(UserDeletedMessage $message): void
    {
        $this->userService->deleteDocument($message->getId());
    }
}