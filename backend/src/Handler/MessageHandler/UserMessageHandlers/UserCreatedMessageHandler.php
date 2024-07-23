<?php

namespace App\Handler\MessageHandler\UserMessageHandlers;

use App\Adapter\DataTransformationAdapter;
use App\Entity\Evaluator;
use App\Entity\User;
use App\Message\UserMessages\UserCreatedMessage;

use App\Services\UserService;
use Doctrine\ODM\MongoDB\MongoDBException;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;

class UserCreatedMessageHandler
{
    public function __construct(
        private DataTransformationAdapter $transformationAdapter,
        private UserService $userService,
    )
    {}

    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     * @throws MongoDBException
     */
    public function __invoke(UserCreatedMessage $message): void
    {
        $userEntity = $this->getEntityFromMessage($message);
        if ($userEntity instanceof Evaluator){
            $userDocument = $this->transformationAdapter->transformToDocument($userEntity, "evaluator");
        }else{
            $userDocument = $this->transformationAdapter->transformToDocument($userEntity, "hr");
        }
        $this->userService->saveDocument($userDocument);
    }
    public function getEntityFromMessage(UserCreatedMessage $message): User
    {
        return $this->userService->findEntity($message->getId());
    }
}