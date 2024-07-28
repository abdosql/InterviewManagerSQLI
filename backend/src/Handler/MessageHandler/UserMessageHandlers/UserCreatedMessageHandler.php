<?php

namespace App\Handler\MessageHandler\UserMessageHandlers;

use App\Adapter\DataTransformationAdapter;
use App\Entity\Evaluator;
use App\Entity\HRManager;
use App\Entity\User;
use App\Message\User\UserCreatedMessage;
use App\Services\Impl\UserService;
use Doctrine\ODM\MongoDB\MongoDBException;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
class UserCreatedMessageHandler
{
    public function __construct(
        private DataTransformationAdapter $transformationAdapter,
        private UserService $userService,
    )
    {}

    /**
     * @param UserCreatedMessage $message
     * @throws ContainerExceptionInterface
     * @throws MongoDBException
     * @throws NotFoundExceptionInterface
     */
    public function __invoke(UserCreatedMessage $message): void
    {
        $userEntity = $this->getEntityFromMessage($message);
        if ($userEntity instanceof Evaluator){
            $userDocument = $this->transformationAdapter->transformToDocument($userEntity, "evaluator");
        }else if($userEntity instanceof HRManager){
            $userDocument = $this->transformationAdapter->transformToDocument($userEntity, "hr");
        }
        $this->userService->saveDocument($userDocument);
    }
    public function getEntityFromMessage(UserCreatedMessage $message): User
    {
        return $this->userService->findEntity($message->getId());
    }
}