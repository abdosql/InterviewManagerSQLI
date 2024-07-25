<?php

namespace App\Handler\MessageHandler\UserMessageHandlers;

use App\Adapter\DataTransformationAdapter;
use App\Document\UserDocument;
use App\Entity\Candidate;
use App\Entity\Evaluator;
use App\Entity\HRManager;
use App\Entity\User;
use App\Message\CandidateMessages\CandidateUpdatedMessage;
use App\Message\UserMessages\UserUpdatedMessage;
use App\Services\Impl\CandidateService;
use App\Services\Impl\UserService;
use Doctrine\ODM\MongoDB\MongoDBException;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
class UserUpdatedMessageHandler
{
    public function __construct(
        private DataTransformationAdapter $transformationAdapter,
        private UserService $userService
    )
    {}

    /**
     * @param UserUpdatedMessage $message
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface|MongoDBException
     */
    public function __invoke(UserUpdatedMessage $message): void
    {
        $userEntity = $this->getUserEntityFromMessage($message);
        $userUpdatedDocument = $this->transformationAdapter->transformToDocument($userEntity, "evaluator");
        if($userEntity instanceof HRManager){
            $userUpdatedDocument = $this->transformationAdapter->transformToDocument($userEntity, "hr");
        }
        $this->userService->updateDocument($message->getId(), $userUpdatedDocument);
    }

    public function getUserEntityFromMessage(UserUpdatedMessage $message): User
    {
        return $this->userService->findEntity($message->getId());
    }
}